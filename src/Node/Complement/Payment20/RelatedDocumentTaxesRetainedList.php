<?php

namespace Angle\CFDI\Node\Complement\Payment20;

use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static RelatedDocumentTaxesRetainedList createFromDOMNode(DOMNode $node)
 */
class RelatedDocumentTaxesRetainedList extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "RetencionesDR";

    const NODE_NS = "pago20";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [];

    protected static $children = [
        'relatedDocumentTaxesRetained' => [
            'keywords'  => ['RetencionDR', 'relatedDocumentTaxesRetained'],
            'class'     => RelatedDocumentTaxesRetained::class,
            'type'      => CFDINode::CHILD_ARRAY,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################


    // CHILDREN NODES
    /**
     * @var RelatedDocumentTaxesRetained[]
     */
    protected $relatedDocumentRetentions = [];



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
                case RelatedDocumentTaxesRetained::NODE_NAME:
                    $retention = RelatedDocumentTaxesRetained::createFromDomNode($node);
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
        foreach ($this->relatedDocumentRetentions as $retention) {
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
     * @return RelatedDocumentTaxesRetained[]
     */
    public function getRelatedDocumentRetentions(): ?array
    {
        return $this->relatedDocumentRetentions;
    }

    /**
     * @param RelatedDocumentTaxesRetained $retention
     * @return RelatedDocumentTaxesRetainedList
     */
    public function addRetention(RelatedDocumentTaxesRetained $retention): self
    {
        $this->relatedDocumentRetentions[] = $retention;
        return $this;
    }

    /**
     * @param TaxesRetained[] $retentions
     * @return RelateddocumentTaxesRetainedList
     */
    public function setRetentions(array $retentions): self
    {
        $this->relatedDocumentRetentions = $retentions;
        return $this;
    }

}