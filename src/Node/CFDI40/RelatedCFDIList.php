<?php

namespace Angle\CFDI\Node\CFDI40;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIException;

use Angle\CFDI\Catalog\RelatedCFDIType;


use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static RelatedCFDIList createFromDOMNode(DOMNode $node)
 */

class RelatedCFDIList extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "CfdiRelacionados";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/4";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'type'           => [
            'keywords' => ['TipoRelacion', 'type'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        'related' => [
            'keywords'  => ['CfdiRelacionado', 'related'],
            'class'     => RelatedCFDI::class,
            'type'      => CFDINode::CHILD_ARRAY,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @see RelatedCFDIType
     * @var string
     */
    protected $type;

    // CHILDREN NODES
    /**
     * @var RelatedCFDI[]
     */
    protected $related = [];


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
                case RelatedCFDI::NODE_NAME:
                    $related = RelatedCFDI::createFromDomNode($node);
                    $this->addRelatedCFDI($related);
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

        // Related node (array)
        foreach ($this->related as $related) {
            $relatedNode = $related->toDOMElement($dom);
            $node->appendChild($relatedNode);
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
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return RelatedCFDIList
     */
    public function setType(?string $type): self
    {
        $this->type = $type;
        return $this;
    }



    #########################
    ## CHILDREN
    #########################

    /**
     * @return RelatedCFDI[]
     */
    public function getRelatedCFDI(): ?array
    {
        return $this->related;
    }

    /**
     * @param RelatedCFDI $related
     * @return RelatedCFDIList
     */
    public function addRelatedCFDI(RelatedCFDI $related): self
    {
        $this->related[] = $related;
        return $this;
    }

    /**
     * @param RelatedCFDI[] $related
     * @return RelatedCFDIList
     */
    public function setRelatedCFDI(array $related): self
    {
        $this->related = $related;
        return $this;
    }


}