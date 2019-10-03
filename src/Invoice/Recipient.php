<?php

namespace Angle\CFDI\Invoice;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use DateTime;

use DOMDocument;
use DOMElement;
use DOMNode;

class Recipient
{
    const NODE_NAME = "cfdi:Receptor";

    /**
     * @var string
     */
    protected $rfc;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $invoiceUse; // UsoCFDI


    #########################
    ##     CONSTRUCTOR     ##
    #########################

    /**
     * Invoice constructor.
     * @param array $data [$attributeName => $value]
     * @throws CFDIException
     */
    public function __construct(array $data)
    {
        // Lookup each element in the given array, attempt to find the corresponding property even if the input is in english or spanish
        foreach ($data as $key => $value) {
            // If the property is in the "base attributes" list, ignore it.
            if (array_key_exists($key, $this->baseAttributes())) {
                continue;
            }

            // Find the corresponding propertyName from the current attribute key
            $propertyName = $this->findPropertyName($key);

            if ($propertyName === null) {
                // Attribute name not found.
                throw new CFDIException("Invalid Attribute Name given, '$key' not found in Recipient object definition.", -1); // TODO: Pelos: add a proper code
            }

            $setter = 'set' . ucfirst($propertyName);
            if (!method_exists(self::class, $setter)) {
                throw new CFDIException("Property '$propertyName' has no setter method.", -1); // TODO: Pelos: add a proper code
            }


            // If the setter fails, it'll throw a CFDIException. We'll let it arise, the final library user should be the one catching and handling these type of exceptions.
            $this->$setter($value);
        }
    }

    /**
     * @param DOMNode $node
     * @return Recipient
     * @throws CFDIException
     */
    public static function createFromDomNode(DOMNode $node): self
    {
        // Extract invoice data
        $data = [];

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $data[$attr->nodeName] = $attr->nodeValue;
            }
        }

        //echo "Invoice data:" . PHP_EOL;
        //print_r($invoiceData);

        try {
            $recipient = new Recipient($data);
        } catch (CFDIException $e) {
            // TODO: handle this exception
            throw $e;
        }


        return $recipient;
    }


    #########################
    ## INVOICE TO DOM TRANSLATION
    #########################

    public function baseAttributes(): array
    {
        return [];
    }

    public function getAttributes(): array
    {
        // TODO: should _this_ function trigger the validation???
        if (!$this->validate()) {
            throw new CFDIException('Recipient is not validated, cannot pull attributes');
        }

        $attr = $this->baseAttributes();

        // FIXME: We could be pulling this automatically from the same translation array used to populate the new object.

        $attr['Rfc'] = $this->rfc;
        $attr['Nombre'] = $this->name;
        $attr['UsoCFDI'] = $this->invoiceUse;

        return $attr;
    }


    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NODE_NAME);

        foreach ($this->getAttributes() as $attr => $value) {
            $node->setAttribute($attr, $value);
        }


        // no child nodes for Recipient

        return $node;
    }


    #########################
    ## VALIDATION
    #########################

    public function validate(): bool
    {
        // TODO: implement the full set of validation, including type and Business Logic

        return true;
    }

    /**
     * @return string
     */
    public function getRfc(): string
    {
        return $this->rfc;
    }

    /**
     * @param string $rfc
     * @return Recipient
     */
    public function setRfc(string $rfc): self
    {
        $this->rfc = $rfc;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Recipient
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceUse(): string
    {
        return $this->invoiceUse;
    }

    /**
     * @param string $invoiceUse
     * @return Recipient
     */
    public function setInvoiceUse(string $invoiceUse): self
    {
        $this->invoiceUse = $invoiceUse;
        return $this;
    }


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    private static $translationMap = [
        // PropertyName => [spanish (official SAT), english]
        'rfc'           => ['Rfc', 'rfc'],
        'name'          => ['Nombre', 'name'],
        'invoiceUse'    => ['UsoCFDI', 'invoiceUse'],

    ];

    private function findPropertyName($prop): ?string
    {
        foreach (self::$translationMap as $propertyName => $translations) {
            if (in_array($prop, $translations)) {
                return $propertyName;
            }
        }

        return null;
    }

}