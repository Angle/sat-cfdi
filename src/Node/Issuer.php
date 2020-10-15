<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Issuer createFromDOMNode(DOMNode $node)
 */
class Issuer extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Emisor";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'rfc'           => [
            'keywords' => ['Rfc', 'rfc'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'name'          => [
            'keywords' => ['Nombre', 'name'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'regime'        => [
            'keywords' => ['RegimenFiscal', 'regime'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        // PropertyName => ClassName (full namespace)
    ];



    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $rfc;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $regime; // RegimenFiscal



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


        // no child nodes for Issuer

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
    public function getRfc(): string
    {
        return $this->rfc;
    }

    /**
     * @param string $rfc
     * @return Issuer
     */
    public function setRfc(string $rfc): self
    {
        $this->rfc = $rfc;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Issuer
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getRegime(): string
    {
        return $this->regime;
    }

    /**
     * @param string $regime
     * @return Issuer
     */
    public function setRegime(string $regime): self
    {
        $this->regime = $regime;
        return $this;
    }

}