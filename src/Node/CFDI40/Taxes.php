<?php

namespace Angle\CFDI\Node\CFDI40;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIException;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Taxes createFromDOMNode(DOMNode $node)
 */
class Taxes extends CFDINode
{
#########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Impuestos";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/4";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'totalRetainedAmount'           => [
            'keywords' => ['TotalImpuestosRetenidos', 'totalRetainedAmount'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalTransferredAmount'          => [
            'keywords' => ['TotalImpuestosTrasladados', 'totalTransferredAmount'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
    ];

    protected static $children = [
        'transferredList' => [
            'keywords'  => ['Traslados', 'transferredList'],
            'class'     => TaxesTransferredList::class,
            'type'      => CFDINode::CHILD_UNIQUE,
        ],
        'retainedList' => [
            'keywords'  => ['Retenciones', 'retainedList'],
            'class'     => TaxesRetainedList::class,
            'type'      => CFDINode::CHILD_UNIQUE,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string|null
     */
    protected $totalRetainedAmount;

    /**
     * @var string|null
     */
    protected $totalTransferredAmount;


    // CHILDREN NODES

    /**
     * @var TaxesTransferredList|null
     */
    protected $transferredList;

    /**
     * @var TaxesRetainedList|null
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
                case TaxesTransferredList::NODE_NAME:
                    $transferred = TaxesTransferredList::createFromDomNode($node);
                    $this->setTransferredList($transferred);
                    break;
                case TaxesRetainedList::NODE_NAME:
                    $retained = TaxesRetainedList::createFromDomNode($node);
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

    /**
     * @return string|null
     */
    public function getTotalRetainedAmount(): ?string
    {
        return $this->totalRetainedAmount;
    }

    /**
     * @param string|null $totalRetainedAmount
     * @return Taxes
     */
    public function setTotalRetainedAmount(?string $totalRetainedAmount): self
    {
        $this->totalRetainedAmount = $totalRetainedAmount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTotalTransferredAmount(): ?string
    {
        return $this->totalTransferredAmount;
    }

    /**
     * @param string|null $totalTransferredAmount
     * @return Taxes
     */
    public function setTotalTransferredAmount(?string $totalTransferredAmount): self
    {
        $this->totalTransferredAmount = $totalTransferredAmount;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return TaxesTransferredList|null
     */
    public function getTransferredList(): ?TaxesTransferredList
    {
        return $this->transferredList;
    }

    /**
     * @param TaxesTransferredList|null $transferredList
     * @return Taxes
     */
    public function setTransferredList(?TaxesTransferredList $transferredList): self
    {
        $this->transferredList = $transferredList;
        return $this;
    }

    /**
     * @return TaxesRetainedList|null
     */
    public function getRetainedList(): ?TaxesRetainedList
    {
        return $this->retainedList;
    }

    /**
     * @param TaxesRetainedList|null $retainedList
     * @return Taxes
     */
    public function setRetainedList(?TaxesRetainedList $retainedList): self
    {
        $this->retainedList = $retainedList;
        return $this;
    }
}