<?php

namespace Angle\CFDI\Node\Complement\Payment20;

use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static RelatedDocumentTaxes createFromDOMNode(DOMNode $node)
 */
class RelatedDocumentTaxes extends CFDINode
{
#########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "ImpuestosDR";
    public const NODE_NAME_EN = 'relatedDocumentTaxes';

    const NODE_NS = "pago20";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
    ];

    protected static $children = [
        'relatedDocumentTaxesRetainedList' => [
            'keywords'  => ['RetencionesDR', 'relatedDocumentTaxesRetainedList'],
            'class'     => RelatedDocumentTaxesRetainedList::class,
            'type'      => CFDINode::CHILD_UNIQUE,
        ],
        'relatedDocumentTaxesTransferredList' => [
            'keywords'  => ['TrasladosDR', 'relatedDocumentTaxesTransferredList'],
            'class'     => RelatedDocumentTaxesTransferredList::class,
            'type'      => CFDINode::CHILD_UNIQUE,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    // no attributes


    // CHILDREN NODES

    /**
     * @var RelatedDocumentTaxesTransferredList|null
     */
    protected $relatedDocumentTransferredList;

    /**
     * @var RelatedDocumentTaxesRetainedList|null
     */
    protected $relatedDocumentRetainedList;


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
                case RelatedDocumentTaxesTransferredList::NODE_NAME:
                    $transferred = RelatedDocumentTaxesTransferredList::createFromDomNode($node);
                    $this->setRelatedDocumentTransferredList($transferred);
                    break;
                case RelatedDocumentTaxesRetainedList::NODE_NAME:
                    $retained = RelatedDocumentTaxesRetainedList::createFromDomNode($node);
                    $this->setRelatedDocumentRetainedList($retained);
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
        if ($this->relatedDocumentTransferredList) {
            // This can be null, no problem if not found
            $transferredListNode = $this->relatedDocumentTransferredList->toDOMElement($dom);
            $node->appendChild($transferredListNode);
        }

        // RetainedList Node
        if ($this->relatedDocumentRetainedList) {
            // This can be null, no problem if not found
            $retainedListNode = $this->relatedDocumentRetainedList->toDOMElement($dom);
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

    // no attributes


    #########################
    ## CHILDREN
    #########################

    /**
     * @return RelatedDocumentTaxesTransferredList|null
     */
    public function getRelatedDocumentTransferredList(): ?RelatedDocumentTaxesTransferredList
    {
        return $this->relatedDocumentTransferredList;
    }

    /**
     * @param RelatedDocumentTaxesTransferredList|null $relatedDocumentTransferredList
     * @return RelatedDocumentTaxes
     */
    public function setRelatedDocumentTransferredList(?RelatedDocumentTaxesTransferredList $relatedDocumentTransferredList): self
    {
        $this->relatedDocumentTransferredList = $relatedDocumentTransferredList;
        return $this;
    }

    /**
     * @return RelatedDocumentTaxesRetainedList|null
     */
    public function getRelatedDocumentRetainedList(): ?RelatedDocumentTaxesRetainedList
    {
        return $this->relatedDocumentRetainedList;
    }

    /**
     * @param RelatedDocumentTaxesRetainedList|null $relatedDocumentRetainedList
     * @return RelatedDocumentTaxes
     */
    public function setRelatedDocumentRetainedList(?RelatedDocumentTaxesRetainedList $relatedDocumentRetainedList): self
    {
        $this->relatedDocumentRetainedList = $relatedDocumentRetainedList;
        return $this;
    }
}