<?php

namespace Angle\CFDI\Node\Complement\LocalTaxes;

use Angle\CFDI\CFDI33;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static LocalTaxesRetained createFromDOMNode(DOMNode $node)
 */
class LocalTaxesRetained extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "RetencionesLocales";

    const NODE_NS = "implocal";
    const NODE_NS_URI = "http://www.sat.gob.mx/implocal";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'tax'           => [
            'keywords' => ['ImpLocRetenido', 'tax'],
            'type'  => CFDINode::ATTR_REQUIRED
        ],
        'rate'          => [
            'keywords' => ['TasadeRetencion', 'rate'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'amount'        => [
            'keywords' => ['Importe', 'amount'],
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
    protected $tax;

    /**
     * @var string
     */
    protected $rate;

    /**
     * @var string
     */
    protected $amount;

    // no children


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
     * @return LocalTaxesRetained
     */
    public function setTax(?string $tax): self
    {
        $this->tax = $tax;
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
     * @return LocalTaxesRetained
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
     * @return LocalTaxesRetained
     */
    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

}