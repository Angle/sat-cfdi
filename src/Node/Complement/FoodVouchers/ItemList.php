<?php

namespace Angle\CFDI\Node\Complement\FoodVouchers;

use Angle\CFDI\CFDI33;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

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

    const NODE_NS = "valesdedespensa";
    const NODE_NS_URI = "http://www.sat.gob.mx/valesdedespensa";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [];

    protected static $children = [
        'items' => [
            'keywords'  => ['Concepto', 'items'],
            'class'     => Item::class,
            'type'      => CFDINode::CHILD_ARRAY,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    // CHILDREN NODES
    /**
     * @var Item[]
     */
    protected $items = [];



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
                case Item::NODE_NAME:
                    $item = Item::createFromDomNode($node);
                    $this->addItem($item);
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

        // Items node (array)
        foreach ($this->items as $item) {
            $itemNode = $item->toDOMElement($dom);
            $node->appendChild($itemNode);
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