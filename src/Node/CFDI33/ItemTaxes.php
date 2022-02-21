<?php

namespace Angle\CFDI\Node\CFDI33;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIException;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static ItemTaxes createFromDOMNode(DOMNode $node)
 */
class ItemTaxes extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Impuestos";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [];

    protected static $children = [
        'transferredList' => [
            'keywords'  => ['Traslados', 'transferredList'],
            'class'     => ItemTaxesTransferredList::class,
            'type'      => CFDINode::CHILD_UNIQUE,
        ],
        'retainedList' => [
            'keywords'  => ['Retenciones', 'retainedList'],
            'class'     => ItemTaxesRetainedList::class,
            'type'      => CFDINode::CHILD_UNIQUE,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    // CHILDREN NODES

    /**
     * @var ItemTaxesTransferredList|null
     */
    protected $transferredList;

    /**
     * @var ItemTaxesRetainedList|null
     */
    protected $retainedList;


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
                case ItemTaxesTransferredList::NODE_NAME:
                    $transferred = ItemTaxesTransferredList::createFromDomNode($node);
                    $this->setTransferredList($transferred);
                    break;
                case ItemTaxesRetainedList::NODE_NAME:
                    $retained = ItemTaxesRetainedList::createFromDomNode($node);
                    $this->setRetainedList($retained);
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

        // TransferredList Node
        if ($this->transferredList) {
            // This can be null, no problem if not found
            $transferredListNode = $this->transferredList->toDOMElement($dom);
            $node->appendChild($transferredListNode);
        }

        // RetainedList Node
        if ($this->retainedList) {
            // This can be null, no problem if not found
            $retainedListNode = $this->retainedList->toDOMElement($dom);
            $node->appendChild($retainedListNode);
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
     * @return ItemTaxesTransferredList|null
     */
    public function getTransferredList(): ?ItemTaxesTransferredList
    {
        return $this->transferredList;
    }

    /**
     * @param ItemTaxesTransferredList|null $transferredList
     * @return ItemTaxes
     */
    public function setTransferredList(?ItemTaxesTransferredList $transferredList): self
    {
        $this->transferredList = $transferredList;
        return $this;
    }

    /**
     * @return ItemTaxesRetainedList|null
     */
    public function getRetainedList(): ?ItemTaxesRetainedList
    {
        return $this->retainedList;
    }

    /**
     * @param ItemTaxesRetainedList|null $retainedList
     * @return ItemTaxes
     */
    public function setRetainedList(?ItemTaxesRetainedList $retainedList): self
    {
        $this->retainedList = $retainedList;
        return $this;
    }
}