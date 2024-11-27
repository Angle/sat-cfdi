<?php

namespace Angle\CFDI;

use Angle\CFDI\CFDIException;

use DateTime;

use DOMDocument;
use DOMElement;
use DOMNode;

abstract class CFDINode implements CFDINodeInterface
{
    private static $baseAttributes = [];
    private static $attributes = [];
    private static $children = [];

    const ATTR_REQUIRED = 'R';
    const ATTR_OPTIONAL = 'O';

    const CHILD_UNIQUE  = 'U';
    const CHILD_ARRAY   = 'A';

    const PROPERTY_NOT_FOUND        = 0;
    const PROPERTY_BASE_ATTRIBUTE   = 1;
    const PROPERTY_ATTRIBUTE        = 2;
    const PROPERTY_CHILDREN         = 3;

    const DATE_FORMAT = 'Y-m-d';
    const DATETIME_FORMAT = 'Y-m-d\TH:i:s';
    const DATETIME_TIMEZONE = 'America/Mexico_City';



    #########################
    ##     CONSTRUCTOR     ##
    #########################

    /**
     * CFDINode constructor.
     * @param array $data [$attributeName => $value]
     * @throws CFDIException
     */
    public function __construct(array $data)
    {
        // debug:
        //echo "Building for: " . $this->getShortName() . ' with data: ' . print_r($data, true) . PHP_EOL;

        // Lookup each element in the given array, attempt to find the corresponding property even if the input is in english or spanish
        foreach ($data as $key => $value) {
            $propertyType = $this->findPropertyType($key);
            if ($propertyType == self::PROPERTY_BASE_ATTRIBUTE) {
                // If the property is in the "base attributes" list, ignore it.
                continue;

            } elseif ($propertyType == self::PROPERTY_ATTRIBUTE) {
                // Find the corresponding propertyName from the current attribute key
                $propertyName = $this->findPropertyNameForAttribute($key);

                if ($propertyName === null) {
                    // Attribute name not found.
                    throw new CFDIException(sprintf("Invalid Attribute Name given, '%s' not found in %s object definition.", $key, $this->getShortName()), -1); // TODO: Add a proper error code
                }

                $setter = 'set' . ucfirst($propertyName);
                if (!method_exists(static::class, $setter)) {
                    // Setter not found
                    throw new CFDIException(sprintf("Property '%s' has no setter method in %s", $propertyName, $this->getShortName()), -1); // TODO: Add a proper error code
                }

                // If the setter fails, it'll throw a CFDIException. We'll let it arise, the final library user should be the one catching and handling these type of exceptions.
                $this->$setter($value);

            } elseif ($propertyType == self::PROPERTY_CHILDREN) {
                // Process each child node
                if (!is_array($value)) {
                    throw new CFDIException(sprintf("Data key '%s' is a Child Node of %s, expecting an array with the child properties, instead got: %s", $key, $this->getShortName(), gettype($value)), -1); // TODO: Add a proper error code
                }

                $this->setChildFromData($key, $value);

            } else {
                throw new CFDIException(sprintf("Data key '%s' is not a valid property for %s.", $key, $this->getShortName()), -1); // TODO: Add a proper error code
            }
        }
    }

    /**
     * @param string $childName node/property name
     * @param array $data [$attributeName => $value]
     * @throws CFDIException
     */
    public function setChildFromData(string $childName, array $data): void
    {
        $propertyName = null;
        $properties = null;
        foreach (static::$children as $key => $prop) {
            if (in_array($childName, $prop['keywords'])) {
                // found a children with a matching name
                $propertyName = $key;
                $properties = $prop;
            }
        }

        if (!$properties) {
            throw new CFDIException(sprintf("Missing child properties definition in %s for child '%s'", $this->getShortName(), $childName), -1); // TODO: Add a proper error code
        }

        if (!array_key_exists('class', $properties) || !array_key_exists('type', $properties)) {
            throw new CFDIException(sprintf("Invalid child properties definition in %s for child '%s'", $this->getShortName(), $childName), -1); // TODO: Add a proper error code
        }

        $childClass = $properties['class'];
        $childType = $properties['type'];

        // The "Type" property dictates how we should append the Child to the working object
        if ($childType == CFDINode::CHILD_UNIQUE) {
            // this is the only child of it's type, we can attach it with it's setter
            $setter = 'set' . ucfirst($propertyName);
            if (!method_exists(static::class, $setter)) {
                // Setter not found
                throw new CFDIException(sprintf("Setter method '%s' does not exist in %s", $setter, $this->getShortName()), -1); // TODO: Add a proper error code
            }

            // Initialize a Child entity
            /** @var CFDINode $child */
            $child = new $childClass($data);

            // Set the child!
            $this->$setter($child);

        } elseif ($childType == CFDINode::CHILD_ARRAY) {
            // there are many of this child, we have to attach it with an "append" method
            $append = 'add' . ucfirst($this->getClassName($childClass));
            if (!method_exists(static::class, $append)) {
                // Setter not found
                throw new CFDIException(sprintf("Append method '%s' does not exist in %s", $append, $this->getShortName()), -1); // TODO: Add a proper error code
            }

            // now we must initialize and append a child for every item in the array
            foreach ($data as $k => $v) {
                if (!is_array($v)) {
                    throw new CFDIException(sprintf("Child '%s' is registered as array in %s, expecting an array of arrays, got '%s' in child %d", $childName, $this->getShortName(), gettype($v), $k), -1); // TODO: Add a proper error code
                }

                // Initialize a Child entity
                /** @var CFDINode $child */
                $child = new $childClass($v);

                // Append the child!
                $this->$append($child);
            }

        } else {
            throw new CFDIException(sprintf("Invalid child type definition in %s for child '%s'", $this->getShortName(), $childName), -1); // TODO: Add a proper error code
        }
    }

    /**
     * @param DOMNode $node
     * @return CFDINode
     * @throws CFDIException
     */
    public static function createFromDOMNode(DOMNode $node): self
    {
        // Extract node attribute data
        $attributes = [];

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                /* Removed. Some complements actually do use a pipe character, such as the original chain
                // Pipe characters are strictly forbidden, they interfere with the Cryptographic signature validation
                if (strpos($attr->nodeValue, '|') !== false) {
                    throw new CFDIException(sprintf("Pipe character '|' cannot appear on any attribute value. [%s - %s: %s]", $node->nodeName, $attr->nodeName, $attr->nodeValue));
                }
                */

                $attributes[$attr->nodeName] = $attr->nodeValue;
            }
        }

        $children = [];
        if ($node->hasChildNodes()) {
            $children = iterator_to_array($node->childNodes);
        }

        try {
            $cfdiNode = new static($attributes);
            $cfdiNode->setChildrenFromDOMNodes($children);
        } catch (CFDIException $e) {
            // TODO: handle this exception
            throw $e;
        }

        return $cfdiNode;
    }

    #########################
    ## HELPER FUNCTIONS
    #########################

    // Helper for DOM Translations
    /**
     * @return array
     * @throws CFDIException
     */
    public function getAttributes(): array
    {
        // TODO: should _this_ function trigger a validation???
        if (!$this->validate()) {
            throw new CFDIException($this->getShortName() . ' is not validated, cannot pull attributes');
        }

        $attr = static::$baseAttributes;

        foreach (static::$attributes as $key => $prop) {
            $get = 'get' . ucfirst($key);
            $value = $this->$get();

            if ($value instanceof DateTime) {
                $value = $value->format(CFDINode::DATETIME_FORMAT);
            }

            if ($prop['type'] == CFDINode::ATTR_REQUIRED && ($value === null || $value === "")) {
                // Required property is not set
                throw new CFDIException(sprintf("Property '%s' is required in %s", ucfirst($key), $this->getShortName()));
            }

            if ($value !== null) {
                $attr[$prop['keywords'][0]] = $value;
            }
        }

        return $attr;
    }

    protected function findPropertyNameForAttribute($name): ?string
    {
        foreach (static::$attributes as $propertyName => $prop) {
            if (in_array($name, $prop['keywords'])) {
                return $propertyName;
            }
        }

        return null;
    }

    protected function findPropertyNameForChild($name): ?string
    {
        foreach (static::$children as $propertyName => $prop) {
            if (in_array($name, $prop['keywords'])) {
                return $propertyName;
            }
        }

        return null;
    }

    /**
     * @param string $name the property to lookup
     * @return int
     */
    protected function findPropertyType($name): int
    {
        if (array_key_exists($name, static::$baseAttributes)) {
            return self::PROPERTY_BASE_ATTRIBUTE;
        }

        foreach (static::$attributes as $propertyName => $prop) {
            if (in_array($name, $prop['keywords'])) {
                // found an attribute (self) with a matching name
                return self::PROPERTY_ATTRIBUTE;
            }
        }

        foreach (static::$children as $propertyName => $prop) {
            if (in_array($name, $prop['keywords'])) {
                // found an attribute (self) with a matching name
                return self::PROPERTY_CHILDREN;
            }
        }

        return self::PROPERTY_NOT_FOUND;
    }

    protected function getShortName()
    {
        try {
            $c = (new \ReflectionClass($this))->getShortName();
        } catch (\Exception $e) {
            $c = '???';
        }

        return $c;
    }

    // non-namespaced
    protected function getClassName($classname)
    {
        if ($pos = strrpos($classname, '\\')) return substr($classname, $pos + 1);
        return $pos;
    }
}