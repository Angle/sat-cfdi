<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * @method static ItemPropertyTaxAccount createFromDOMNode(DOMNode $node)
 */
class ItemPropertyTaxAccount extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "CuentaPredial";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'number'           => [
            'keywords' => ['Numero', 'number'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];

    protected static $children = [];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $number;


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

        // no child nodes

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
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param string $number
     * @return ItemPropertyTaxAccount
     */
    public function setNumber(?string $number): self
    {
        $this->number = $number;
        return $this;
    }
}