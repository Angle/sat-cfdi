<?php

namespace Angle\CFDI\Node\Complement\Payment20;

use Angle\CFDI\CFDIException;
use Angle\CFDI\CFDINode;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static RelatedDocumentTaxesTransferredList createFromDOMNode(DOMNode $node)
 */
class RelatedDocumentTaxesTransferredList extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    public const NODE_NAME = "TrasladosDR";
    public const NODE_NAME_EN = 'relatedDocumentTaxesTransferredList';

    public const NODE_NS = "pago20";
    public const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    public const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    public const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [];

    protected static $children = [
        'relatedDocumentTaxesTransferred' => [
            'keywords' => ['TrasladoDR', 'relatedDocumentTaxesTransferred'],
            'class' => RelatedDocumentTaxesTransferred::class,
            'type' => CFDINode::CHILD_ARRAY,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################


    // CHILDREN NODES
    /**
     * @var RelatedDocumentTaxesTransferred[]
     */
    protected $relatedDocumentTaxesTransferred = [];


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
                case RelatedDocumentTaxesTransferred::NODE_NAME:
                    $transfer = RelatedDocumentTaxesTransferred::createFromDomNode($node);
                    $this->addRelatedDocumentTaxesTransferred($transfer);
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
        foreach ($this->relatedDocumentTaxesTransferred as $transfer) {
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
     * @return RelatedDocumentTaxesTransferred[]
     */
    public function getRelatedDocumentTaxesTransferred(): ?array
    {
        return $this->relatedDocumentTaxesTransferred;
    }

    /**
     * @param RelatedDocumentTaxesTransferred $transfer
     * @return RelatedDocumentTaxesTransferredList
     */
    public function addRelatedDocumentTaxesTransferred(RelatedDocumentTaxesTransferred $transfer): self
    {
        $this->relatedDocumentTaxesTransferred[] = $transfer;
        return $this;
    }

    /**
     * @param TaxesTransferred[] $transfers
     * @return RelatedDocumentTaxesTransferredList
     */
    public function setRelatedDocumentTaxesTransferred(array $transfers): self
    {
        $this->relatedDocumentTaxesTransferred = $transfers;
        return $this;
    }

}