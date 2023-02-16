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

class GlobalInformation extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "InformacionGlobal";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/4";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'frequency'           => [
            'keywords' => ['Periodicidad', 'frequency'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'months'           => [
            'keywords' => ['Meses', 'months'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'year'           => [
            'keywords' => ['Año', 'year'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        // none
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $frequency;

    /**
     * @var string
     */
    protected $months;

    /**
     * @var int
     */
    protected $year;

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
        foreach ($children as $node) {
            if ($node instanceof DOMText) {
                // TODO: we are skipping the actual text inside the Node.. is this useful?
                continue;
            }

            // no children nodes
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
    public function getFrequency(): ?string
    {
        return $this->frequency;
    }

    /**
     * @param string $frequency
     * @return self
     */
    public function setFrequency(?string $frequency): self
    {
        $this->frequency = $frequency;
        return $this;
    }

    /**
     * @return string
     */
    public function getMonths(): ?string
    {
        return $this->months;
    }

    /**
     * @param string $months
     * @return self
     */
    public function setMonths(?string $months): self
    {
        $this->months = $months;
        return $this;
    }

    /**
     * @return int
     */
    public function getYear(): ?int
    {
        return $this->year;
    }

    /**
     * @param int $year
     * @return self
     */
    public function setYear(?int $year): self
    {
        $this->year = $year;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    // no children

}