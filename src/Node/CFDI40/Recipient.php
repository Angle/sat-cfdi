<?php

namespace Angle\CFDI\Node\CFDI40;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIException;

use Angle\CFDI\Catalog\CFDIUse;

use DateTime;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Recipient createFromDOMNode(DOMNode $node)
 */
class Recipient extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Receptor";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/4";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'rfc'           => [
            'keywords' => ['Rfc', 'rfc'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'name'          => [
            'keywords' => ['Nombre', 'name'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'postalCode'    => [
            'keywords' => ['DomicilioFiscalReceptor', 'postalCode'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'foreignCountry'          => [
            'keywords' => ['ResidenciaFiscal', 'foreignCountry'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'foreignTaxCode'          => [
            'keywords' => ['NumRegIdTrib', 'foreignTaxCode'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'regime'        => [
            'keywords' => ['RegimenFiscalReceptor', 'regime'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'cfdiUse'    => [
            'keywords' => ['UsoCFDI', 'cfdiUse'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
    ];

    protected static $children = [];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $rfc;

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @var string
     */
    protected $postalCode;

    /**
     * @var string
     */
    protected $regime; // Régimen fiscal

    /**
     * @var string
     * @see CFDIUse
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
    public function setChildrenFromDOMNodes(array $children): void
    {
        // void
    }


    #########################
    ## CFDI NODE TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElementNS(self::NODE_NS_URI, self::NODE_NS_NAME);

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
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
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
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return self
     */
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegime(): ?string
    {
        return $this->regime;
    }

    /**
     * @param string $regime
     * @return self
     */
    public function setRegime(?string $regime): self
    {
        $this->regime = $regime;
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