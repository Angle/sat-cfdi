<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use Angle\CFDI\Catalog\TaxFactorType;
use Angle\CFDI\Catalog\TaxType;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static TaxesTransferred createFromDOMNode(DOMNode $node)
 */
class TaxesTransferred extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Traslado";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'tax'          => [
            'keywords' => ['Impuesto', 'tax'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'factorType'        => [
            'keywords' => ['TipoFactor', 'factorType'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'rate'        => [
            'keywords' => ['TasaOCuota', 'rate'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'amount'        => [
            'keywords' => ['Importe', 'amount'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        // PropertyName => ClassName (full namespace)
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @see TaxType
     * @var string
     */
    protected $tax;

    /**
     * @see TaxFactorType
     * @var string
     */
    protected $factorType;

    /**
     * @var string
     */
    protected $rate;

    /**
     * @var string
     */
    protected $amount;


    // CHILDREN NODES
    // none



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

        // no children

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
    ##   SPECIAL METHODS   ##
    #########################

    public function getTaxName($lang='es')
    {
        return TaxType::getName($this->tax, $lang);
    }

    public function getFactorTypeName($lang='es')
    {
        return TaxFactorType::getName($this->factorType, $lang);
    }


    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string
     */
    public function getTax(): ?string
    {
        return $this->tax;
    }

    /**
     * @param string $tax
     * @return TaxesTransferred
     */
    public function setTax(?string $tax): self
    {
        // TODO: Use TaxType
        $this->tax = $tax;
        return $this;
    }

    /**
     * @return string
     */
    public function getFactorType(): ?string
    {
        return $this->factorType;
    }

    /**
     * @param string $factorType
     * @return TaxesTransferred
     */
    public function setFactorType(?string $factorType): self
    {
        // TODO: use TaxFactorType
        $this->factorType = $factorType;
        return $this;
    }

    /**
     * @return string
     */
    public function getRate(): ?string
    {
        return $this->rate;
    }

    /**
     * @param string $rate
     * @return TaxesTransferred
     */
    public function setRate(?string $rate): self
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return TaxesTransferred
     */
    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    // no children
}