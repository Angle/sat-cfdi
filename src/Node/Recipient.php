<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * @method static Recipient createFromDOMNode(DOMNode $node)
 */
class Recipient extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Receptor";
    const NS_NODE_NAME = "cfdi:Receptor";

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'rfc'           => [
            'keywords' => ['Rfc', 'rfc'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'name'          => [
            'keywords' => ['Nombre', 'name'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'foreignCountry'          => [
            'keywords' => ['ResidenciaFiscal', 'foreignCountry'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'foreignTaxCode'          => [
            'keywords' => ['NumRegIdTrib', 'foreignTaxCode'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'cfdiUse'    => [
            'keywords' => ['UsoCFDI', 'cfdiUse'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

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
    protected $cfdiUse; // UsoCFDI

    /**
     * @var string
     */
    protected $foreignCountry; // ResidenciaFiscal

    /**
     * @var string
     */
    protected $foreignTaxCode; // Número de Registro de Identidad Fiscal para residentes en el extranjero


    #########################
    ##     CONSTRUCTOR     ##
    #########################

    // constructor implemented in the CFDINode abstract class

    /**
     * @param DOMNode[]
     * @throws CFDIException
     */
    public function setChildren(array $children): void
    {
        // void
    }


    #########################
    ## CFDI NODE TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NS_NODE_NAME);

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



    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string
     */
    public function getRfc(): ?string
    {
        return $this->rfc;
    }

    /**
     * @param string $rfc
     * @return Recipient
     */
    public function setRfc(?string $rfc): self
    {
        $this->rfc = $rfc;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Recipient
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getCfdiUse(): ?string
    {
        return $this->cfdiUse;
    }

    /**
     * @param string $cfdiUse
     * @return Recipient
     */
    public function setCfdiUse(?string $cfdiUse): self
    {
        $this->cfdiUse = $cfdiUse;
        return $this;
    }

    /**
     * @return string
     */
    public function getForeignCountry(): ?string
    {
        return $this->foreignCountry;
    }

    /**
     * @param string $foreignCountry
     * @return Recipient
     */
    public function setForeignCountry(?string $foreignCountry): self
    {
        $this->foreignCountry = $foreignCountry;
        return $this;
    }

    /**
     * @return string
     */
    public function getForeignTaxCode(): ?string
    {
        return $this->foreignTaxCode;
    }

    /**
     * @param string $foreignTaxCode
     * @return Recipient
     */
    public function setForeignTaxCode(?string $foreignTaxCode): self
    {
        $this->foreignTaxCode = $foreignTaxCode;
        return $this;
    }
}