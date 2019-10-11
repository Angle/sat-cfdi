<?php

namespace Angle\CFDI\Invoice;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use DateTime;

use DOMDocument;
use DOMElement;
use DOMNode;

abstract class CFDINode implements CFDINodeInterface
{
    private static $baseAttributes = [];

    private static $attributes = [
        // PropertyName => [spanish (official SAT), english]
    ];


    #########################
    ##     CONSTRUCTOR     ##
    #########################

    /**
     * CFDINode constructor.
     * @param array $attributes [$attributeName => $value]
     * @throws CFDIException
     */
    public function __construct(array $attributes, array $children)
    {
        // Lookup each element in the given array, attempt to find the corresponding property even if the input is in english or spanish
        foreach ($attributes as $key => $value) {
            // If the property is in the "base attributes" list, ignore it.
            if (array_key_exists($key, static::$baseAttributes)) {
                continue;
            }

            // Find the corresponding propertyName from the current attribute key
            $propertyName = $this->findPropertyName($key);

            if ($propertyName === null) {
                // Attribute name not found.
                try {
                    $c = (new \ReflectionClass($this))->getShortName();
                } catch (\Exception $e) {
                    $c = '???';
                }

                throw new CFDIException("Invalid Attribute Name given, '$key' not found in $c object definition.", -1); // TODO: Add a proper error code
            }

            $setter = 'set' . ucfirst($propertyName);
            if (!method_exists(static::class, $setter)) {
                try {
                    $c = (new \ReflectionClass($this))->getShortName();
                } catch (\Exception $e) {
                    $c = '???';
                }

                throw new CFDIException("Property '$propertyName' has no setter method in $c.", -1); // TODO: Add a proper error code
            }

            // If the setter fails, it'll throw a CFDIException. We'll let it arise, the final library user should be the one catching and handling these type of exceptions.
            $this->$setter($value);
        }

        // Process each child node
        $this->setChildren($children);
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
                // Pipe characters are strictly forbidden, they interfere with the Cryptographic signature validation
                if (strpos($attr->nodeValue, '|') !== false) {
                    throw new CFDIException(sprintf("Pipe character '|' cannot appear on any attribute value. [%s - %s: %s]", $node->nodeName, $attr->nodeName, $attr->nodeValue));
                }

                $attributes[$attr->nodeName] = $attr->nodeValue;
            }
        }

        $children = [];
        if ($node->hasChildNodes()) {
            $children = iterator_to_array($node->childNodes);
        }

        try {
            $cfdiNode = new static($attributes, $children);
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
            try {
                $c = (new \ReflectionClass($this))->getShortName();
            } catch (\Exception $e) {
                $c = '???';
            }

            throw new CFDIException($c . ' is not validated, cannot pull attributes');
        }

        $attr = static::$baseAttributes;

        foreach (static::$attributes as $key => $prop) {
            $get = 'get' . ucfirst($key);
            $value = $this->$get();

            if ($value instanceof DateTime) {
                $value = $value->format(CFDI::DATETIME_FORMAT);
            }

            if ($prop['type'] == CFDI::ATTR_REQUIRED && ($value === null || $value === "")) {
                try {
                    $c = (new \ReflectionClass($this))->getShortName();
                } catch (\Exception $e) {
                    $c = '???';
                }

                throw new CFDIException(sprintf("Property '%s' is required in %s", ucfirst($key), $c));
            }

            if ($value !== null) {
                $attr[$prop['keywords'][0]] = $value;
            }
        }

        return $attr;
    }

    protected function findPropertyName($name): ?string
    {
        foreach (static::$attributes as $propertyName => $prop) {
            if (in_array($name, $prop['keywords'])) {
                return $propertyName;
            }
        }

        return null;
    }
}