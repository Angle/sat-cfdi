<?php

namespace Angle\CFDI\Node\CFDI40;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIException;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static ItemThirdParties createFromDOMNode(DOMNode $node)
 */
class ItemThirdParties extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "ACuentaTerceros";

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
        'thirdPartyRfc'           => [
            'keywords' => ['RfcACuentaTerceros', 'thirdPartyRfc'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'thirdPartyName'           => [
            'keywords' => ['NombreACuentaTerceros', 'thirdPartyName'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'thirdPartyRegime'           => [
            'keywords' => ['RegimenFiscalACuentaTerceros', 'thirdPartyRegime'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'thirdPartyPostalCode'           => [
            'keywords' => ['DomicilioFiscalACuentaTerceros', 'thirdPartyPostalCode'],
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
    protected $thirdPartyRfc;

    /**
     * @var string
     */
    protected $thirdPartyName;

    /**
     * @var string
     */
    protected $thirdPartyRegime;

    /**
     * @var string
     */
    protected $thirdPartyPostalCode;


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
    public function getThirdPartyRfc(): ?string
    {
        return $this->thirdPartyRfc;
    }

    /**
     * @param string|null $thirdPartyRfc
     * @return self
     */
    public function setThirdPartyRfc(?string $thirdPartyRfc): self
    {
        $this->thirdPartyRfc = $thirdPartyRfc;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getThirdPartyName(): ?string
    {
        return $this->thirdPartyName;
    }

    /**
     * @param string|null $thirdPartyName
     * @return self
     */
    public function setThirdPartyName(?string $thirdPartyName): self
    {
        $this->thirdPartyName = $thirdPartyName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getThirdPartyRegime(): ?string
    {
        return $this->thirdPartyRegime;
    }

    /**
     * @param string|null $thirdPartyRegime
     * @return self
     */
    public function setThirdPartyRegime(?string $thirdPartyRegime): self
    {
        $this->thirdPartyRegime = $thirdPartyRegime;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getThirdPartyPostalCode(): ?string
    {
        return $this->thirdPartyPostalCode;
    }

    /**
     * @param string|null $thirdPartyPostalCode
     * @return self
     */
    public function setThirdPartyPostalCode(?string $thirdPartyPostalCode): self
    {
        $this->thirdPartyPostalCode = $thirdPartyPostalCode;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    // none.
}