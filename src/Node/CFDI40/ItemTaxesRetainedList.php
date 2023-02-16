<?php

namespace Angle\CFDI\Node\CFDI40;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIException;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static ItemTaxesRetainedList createFromDOMNode(DOMNode $node)
 */
class ItemTaxesRetainedList extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Retenciones";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/4";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [];

    protected static $children = [
        'retentions' => [
            'keywords'  => ['Retencion', 'retentions'],
            'class'     => ItemTaxesRetained::class,
            'type'      => CFDINode::CHILD_ARRAY,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################


    // CHILDREN NODES
    /**
     * @var ItemTaxesRetained[]
     */
    protected $retentions = [];



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
        foreach ($children as $node) {
            if ($node instanceof DOMText) {
                // TODO: we are skipping the actual text inside the Node.. is this useful?
                continue;
            }

            switch ($node->localName) {
                case ItemTaxesRetained::NODE_NAME:
                    $retention = ItemTaxesRetained::createFromDomNode($node);
                    $this->addRetention($retention);
                    break;
                default:
                    throw new CFDIException(sprintf("Unknown children node '%s' in %s", $node->nodeName, self::NODE_NS_NAME));
            }
        }
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

        // Retentions node (array)
        foreach ($this->retentions as $retention) {
            $retentionNode = $retention->toDOMElement($dom);
            $node->appendChild($retentionNode);
        }

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

    // none


    #########################
    ## CHILDREN
    #########################

    /**
     * @return ItemTaxesRetained[]
     */
    public function getRetentions(): ?array
    {
        return $this->retentions;
    }

    /**
     * @param ItemTaxesRetained[] $retentions
     * @return ItemTaxesRetainedList
     */
    public function setRetentions(array $retentions): self
    {
        $this->retentions = $retentions;
        return $this;
    }

    /**
     * @param ItemTaxesRetained $retention
     * @return ItemTaxesRetainedList
     */
    public function addRetention(ItemTaxesRetained $retention): self
    {
        $this->retentions[] = $retention;
        return $this;
    }

    /**
     * alias of addRetention
     * @param ItemTaxesRetained $retention
     * @return ItemTaxesRetainedList
     */
    public function addItemTaxesRetained(ItemTaxesRetained $retention): self
    {
        return $this->addRetention($retention);
    }
}