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
 * @method static ItemList createFromDOMNode(DOMNode $node)
 */
class ItemList extends CFDINode
{

    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Conceptos";
    const NS_NODE_NAME = "cfdi:Conceptos";

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [];



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
        foreach ($children as $node) {
            if ($node instanceof DOMText) {
                // TODO: we are skipping the actual text inside the Node.. is this useful?
                continue;
            }

            switch ($node->localName) {
                case Item::NODE_NAME:
                    $item = Item::createFromDomNode($node);
                    $this->addItem($item);
                    break;
                default:
                    throw new CFDIException(sprintf("Unknown children node '%s' in %s", $node->localName, self::NODE_NAME));
            }
        }
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


        // TODO: Child nodes

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
    ##      PROPERTIES     ##
    #########################


    // CHILDREN NODES
    /**
     * @var Item[]
     */
    protected $items;


    #########################
    ## GETTERS AND SETTERS ##
    #########################

    // none


    #########################
    ## CHILDREN
    #########################

    /**
     * @return Item[]
     */
    public function getItems(): ?array
    {
        return $this->items;
    }

    /**
     * @param Item $item
     * @return ItemList
     */
    public function addItem(Item $item): self
    {
        $this->items[] = $item;
        return $this;
    }

    /**
     * @param Item[] $items
     * @return ItemList
     */
    public function setItems(array $items): self
    {
        $this->items = $items;
        return $this;
    }
}