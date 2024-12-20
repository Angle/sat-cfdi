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

    public const NODE_NAME = "ImpuestosDR";
    public const NODE_NAME_EN = 'relatedDocumentTaxes';

    public const NODE_NS = "pago20";
    public const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    public const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    public const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
    ];

    protected static $children = [
        'relatedDocumentTaxesRetainedList' => [
            'keywords' => ['RetencionesDR', 'relatedDocumentTaxesRetainedList'],
            'class' => RelatedDocumentTaxesRetainedList::class,
            'type' => CFDINode::CHILD_UNIQUE,
        ],
        'relatedDocumentTaxesTransferredList' => [
            'keywords' => ['TrasladosDR', 'relatedDocumentTaxesTransferredList'],
            'class' => RelatedDocumentTaxesTransferredList::class,
            'type' => CFDINode::CHILD_UNIQUE,
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
    protected $relatedDocumentTaxesTransferredList;

    /**
     * @var RelatedDocumentTaxesRetainedList|null
     */
    protected $relatedDocumentTaxesRetainedList;


    #########################
    ##     CONSTRUCTOR     ##
    #########################

    // constructor implemented in the CFDINode abstract class

    /**
     * @param DOMNode[]|array $children
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
                    $this->setRelatedDocumentTaxesTransferredList($transferred);
                    break;
                case RelatedDocumentTaxesRetainedList::NODE_NAME:
                    $retained = RelatedDocumentTaxesRetainedList::createFromDomNode($node);
                    $this->setRelatedDocumentTaxesRetainedList($retained);
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

        // RetainedList Node
        if ($this->relatedDocumentTaxesRetainedList) {
            // This can be null, no problem if not found
            $retainedListNode = $this->relatedDocumentTaxesRetainedList->toDOMElement($dom);
            $node->appendChild($retainedListNode);
        }

        // TransferredList Node
        if ($this->relatedDocumentTaxesTransferredList) {
            // This can be null, no problem if not found
            $transferredListNode = $this->relatedDocumentTaxesTransferredList->toDOMElement($dom);
            $node->appendChild($transferredListNode);
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

    public function getRelatedDocumentTaxesTransferredList(): ?RelatedDocumentTaxesTransferredList
    {
        return $this->relatedDocumentTaxesTransferredList;
    }

    public function setRelatedDocumentTaxesTransferredList(?RelatedDocumentTaxesTransferredList $relatedDocumentTaxesTransferredList): void
    {
        $this->relatedDocumentTaxesTransferredList = $relatedDocumentTaxesTransferredList;
    }

    public function getRelatedDocumentTaxesRetainedList(): ?RelatedDocumentTaxesRetainedList
    {
        return $this->relatedDocumentTaxesRetainedList;
    }

    public function setRelatedDocumentTaxesRetainedList(?RelatedDocumentTaxesRetainedList $relatedDocumentTaxesRetainedList): void
    {
        $this->relatedDocumentTaxesRetainedList = $relatedDocumentTaxesRetainedList;
    }

    #########################
    ## CHILDREN
    #########################


}