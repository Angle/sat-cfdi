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
 * @method static Taxes createFromDOMNode(DOMNode $node)
 */
class Taxes extends CFDINode
{
#########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Impuestos";
    const NS_NODE_NAME = "cfdi:Impuestos";

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'totalRetainedAmount'           => [
            'keywords' => ['TotalImpuestosRetenidos', 'totalRetainedAmount'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'totalTranslatedAmount'          => [
            'keywords' => ['TotalImpuestosTrasladados', 'totalTranslatedAmount'],
            'type' => CFDI::ATTR_OPTIONAL
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
    protected $totalTranslatedAmount;


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
    public function setChildren(array $children): void
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
                    throw new CFDIException(sprintf("Unknown children node '%s' in %s", $node->localName, self::NODE_NAME));
            }
        }
    }


    #########################
    ## CFDI NODE TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NS_NODE_NAME);

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
    public function getTotalTranslatedAmount(): ?string
    {
        return $this->totalTranslatedAmount;
    }

    /**
     * @param string|null $totalTranslatedAmount
     * @return Taxes
     */
    public function setTotalTranslatedAmount(?string $totalTranslatedAmount): self
    {
        $this->totalTranslatedAmount = $totalTranslatedAmount;
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