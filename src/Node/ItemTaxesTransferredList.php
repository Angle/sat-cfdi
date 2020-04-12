<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static ItemTaxesTransferredList createFromDOMNode(DOMNode $node)
 */
class ItemTaxesTransferredList extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Traslados";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [];

    protected static $children = [
        // PropertyName => ClassName (full namespace)
    ];


    #########################
    ##      PROPERTIES     ##
    #########################


    // CHILDREN NODES
    /**
     * @var ItemTaxesTransferred[]
     */
    protected $transfers = [];



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
                case ItemTaxesTransferred::NODE_NAME:
                    $transfer = ItemTaxesTransferred::createFromDomNode($node);
                    $this->addTransfer($transfer);
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

        // Transfers node (array)
        foreach ($this->transfers as $transfer) {
            $transferNode = $transfer->toDOMElement($dom);
            $node->appendChild($transferNode);
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
     * @return ItemTaxesTransferred[]
     */
    public function getTransfers(): ?array
    {
        return $this->transfers;
    }

    /**
     * @param ItemTaxesTransferred $transfer
     * @return ItemTaxesTransferredList
     */
    public function addTransfer(ItemTaxesTransferred $transfer): self
    {
        $this->transfers[] = $transfer;
        return $this;
    }

    /**
     * @param ItemTaxesTransferred[] $transfers
     * @return ItemTaxesTransferredList
     */
    public function setTransfers(array $transfers): self
    {
        $this->transfers = $transfers;
        return $this;
    }
}