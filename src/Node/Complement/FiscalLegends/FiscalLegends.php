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
 * @method static FiscalLegends createFromDOMNode(DOMNode $node)
 */
class FiscalLegends extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION_1_0 = "1.0";

    const NODE_NAME = "LeyendasFiscales";

    const NODE_NS = "leyendasFisc";
    const NODE_NS_URI = "http://www.sat.gob.mx/leyendasFiscales";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [
        'xmlns:leyendasFisc' => "http://www.sat.gob.mx/leyendasFiscales",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/leyendasFiscales http://www.sat.gob.mx/sitio_internet/cfd/leyendasFiscales/leyendasFisc.xsd",
    ];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'version'           => [
            'keywords' => ['version', 'version'],
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
     * @var string
     */
    protected $version = self::VERSION_1_0;


    // CHILDREN NODES
    /**
     * @var Legend[]
     */
    protected $legends = [];


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
                case Legend::NODE_NAME:
                    $legend = Legend::createFromDomNode($node);
                    $this->addLegend($legend);
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

        // Legends Node
        foreach ($this->legends as $legend) {
            $legendNode = $legend->toDOMElement($dom);
            $node->appendChild($legendNode);
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
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return FiscalLegends
     */
    public function setVersion(?string $version): self
    {
        // Note: this value is fixed, it cannot be set or changed
        //$this->version = $version;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return Legend[]
     */
    public function getLegends(): ?array
    {
        return $this->legends;
    }

    /**
     * @param Legend $legend
     * @return FiscalLegends
     */
    public function addLegend(Legend $legend): self
    {
        $this->legends[] = $legend;
        return $this;
    }

    /**
     * @param Legend[] $legends
     * @return FiscalLegends
     */
    public function setLegends(array $legends): self
    {
        $this->legends = $legends;
        return $this;
    }



}