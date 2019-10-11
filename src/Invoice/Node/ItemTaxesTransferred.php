<?php

namespace Angle\CFDI\Invoice\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\Invoice\CFDINode;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static ItemTaxesTransferred createFromDOMNode(DOMNode $node)
 */
class ItemTaxesTransferred extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Traslado";
    const NS_NODE_NAME = "cfdi:Traslado";

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'base'           => [
            'keywords' => ['Base', 'base'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'tax'          => [
            'keywords' => ['Impuesto', 'tax'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'type'        => [
            'keywords' => ['TipoFactor', 'type'],
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


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $base;

    /**
     * @var string
     */
    protected $tax;

    /**
     * @var string
     */
    protected $type;

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
    public function setChildren(array $children): void
    {
        // void
    }


    #########################
    ## INVOICE TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NS_NODE_NAME);

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
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string
     */
    public function getBase(): ?string
    {
        return $this->base;
    }

    /**
     * @param string $base
     * @return ItemTaxesTransferred
     */
    public function setBase(?string $base): self
    {
        $this->base = $base;
        return $this;
    }

    /**
     * @return string
     */
    public function getTax(): ?string
    {
        return $this->tax;
    }

    /**
     * @param string $tax
     * @return ItemTaxesTransferred
     */
    public function setTax(?string $tax): self
    {
        $this->tax = $tax;
        return $this;
    }

    /**
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return ItemTaxesTransferred
     */
    public function setType(?string $type): self
    {
        $this->type = $type;
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
     * @return ItemTaxesTransferred
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
     * @return ItemTaxesTransferred
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