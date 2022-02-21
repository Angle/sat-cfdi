<?php

namespace Angle\CFDI\Node\Complement\FiscalLegends;

use Angle\CFDI\CFDI33;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Legend createFromDOMNode(DOMNode $node)
 */
class Legend extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Leyenda";

    const NODE_NS = "leyendasFisc";
    const NODE_NS_URI = "http://www.sat.gob.mx/leyendasFiscales";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'fiscalDisposition'           => [
            'keywords' => ['disposicionFiscal', 'fiscalDisposition'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'norm'           => [
            'keywords' => ['norma', 'norm'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'text'           => [
            'keywords' => ['textoLeyenda', 'text'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        // PropertyName => ClassName (full namespace)
    ];



    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string|null
     */
    protected $fiscalDisposition;

    /**
     * @var string|null
     */
    protected $norm;

    /**
     * @var string|null
     */
    protected $text;


    // CHILDREN NODES
    // none


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
     * @return string|null
     */
    public function getFiscalDisposition(): ?string
    {
        return $this->fiscalDisposition;
    }

    /**
     * @param string|null $fiscalDisposition
     * @return Legend
     */
    public function setFiscalDisposition(?string $fiscalDisposition): self
    {
        $this->fiscalDisposition = $fiscalDisposition;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNorm(): ?string
    {
        return $this->norm;
    }

    /**
     * @param string|null $norm
     * @return Legend
     */
    public function setNorm(?string $norm): self
    {
        $this->norm = $norm;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @param string|null $text
     * @return Legend
     */
    public function setText(?string $text): self
    {
        $this->text = $text;
        return $this;
    }


}