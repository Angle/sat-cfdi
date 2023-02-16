<?php

namespace Angle\CFDI\Node\CFDI33;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIException;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static ItemCustomsInformation createFromDOMNode(DOMNode $node)
 */
class ItemCustomsInformation extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "InformacionAduanera";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'importDocumentNumber'           => [
            'keywords' => ['NumeroPedimento', 'importDocumentNumber'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
    ];

    protected static $children = [];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $importDocumentNumber;


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
        // void
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

        // no children

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
    public function getImportDocumentNumber(): ?string
    {
        return $this->importDocumentNumber;
    }

    /**
     * @param string $importDocumentNumber
     * @return ItemCustomsInformation
     */
    public function setImportDocumentNumber(?string $importDocumentNumber): self
    {
        $this->importDocumentNumber = $importDocumentNumber;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    // none.
}