<?php

namespace Angle\CFDI\Invoice\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\Invoice\CFDINode;

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
    const NS_NODE_NAME = "cfdi:InformacionAduanera";

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'importDocumentNumber'           => [
            'keywords' => ['NumeroPedimento', 'importDocumentNumber'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];


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
    public function setChildren(array $children): void
    {
        // void
    }


    #########################
    ## INVOICE TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NS_NODE_NAME);

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