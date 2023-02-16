<?php

namespace Angle\CFDI\Node\Complement\ThirdParties;

use Angle\CFDI\CFDI33;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

// TODO: Pending implementation of the "Choice" section inside InformacionFiscalTercero, nodes: InformacionAduanera, Parte, CuentaPredial

/**
 * @method static ThirdParties createFromDOMNode(DOMNode $node)
 */
class ThirdParties extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION_1_1 = "1.1";

    const NODE_NAME = "PorCuentadeTerceros";

    const NODE_NS = "terceros";
    const NODE_NS_URI = "http://www.sat.gob.mx/terceros";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [
        'xmlns:terceros' => "http://www.sat.gob.mx/terceros",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/terceros http://www.sat.gob.mx/sitio_internet/cfd/terceros/terceros11.xsd",
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
        'rfc'           => [
            'keywords' => ['rfc', 'rfc'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'name'           => [
            'keywords' => ['nombre', 'name'],
            'type' => CFDINode::ATTR_OPTIONAL
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
    protected $version = self::VERSION_1_1;

    /**
     * @var string
     */
    protected $rfc;

    /**
     * @var string|null
     */
    protected $name;


    // CHILDREN NODES
    /**
     * @var ThirdPartyInformation|null
     */
    protected $thirdPartyInformation;

    /**
     * @var Taxes[]
     */
    protected $taxes = [];


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
                case ThirdPartyInformation::NODE_NAME:
                    $information = ThirdPartyInformation::createFromDomNode($node);
                    $this->setThirdPartyInformation($information);
                    break;
                case Taxes::NODE_NAME:
                    $taxes = Taxes::createFromDomNode($node);
                    $this->addTaxes($taxes);
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

        // ThirdPartyInformation Node
        if ($this->thirdPartyInformation) {
            $thirdPartyInformationNode = $this->thirdPartyInformation->toDOMElement($dom);
            $node->appendChild($thirdPartyInformationNode);
        }

        // Taxes Node
        foreach ($this->taxes as $taxes) {
            $taxesNode = $taxes->toDOMElement($dom);
            $node->appendChild($taxesNode);
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
     * @return ThirdParties
     */
    public function setVersion(?string $version): self
    {
        // Note: this value is fixed, it cannot be set or changed
        //$this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getRfc(): ?string
    {
        return $this->rfc;
    }

    /**
     * @param string $rfc
     * @return ThirdParties
     */
    public function setRfc(?string $rfc): self
    {
        $this->rfc = $rfc;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return ThirdParties
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return ThirdPartyInformation|null
     */
    public function getThirdPartyInformation(): ?ThirdPartyInformation
    {
        return $this->thirdPartyInformation;
    }

    /**
     * @param ThirdPartyInformation|null $thirdPartyInformation
     * @return ThirdParties
     */
    public function setThirdPartyInformation(?ThirdPartyInformation $thirdPartyInformation): self
    {
        $this->thirdPartyInformation = $thirdPartyInformation;
        return $this;
    }

    /**
     * @return Taxes[]
     */
    public function getTaxes(): ?array
    {
        return $this->taxes;
    }

    /**
     * @param Taxes $taxes
     * @return ThirdParties
     */
    public function addTaxes(Taxes $taxes): self
    {
        $this->taxes[] = $taxes;
        return $this;
    }

    /**
     * @param Taxes[] $taxes
     * @return ThirdParties
     */
    public function setTaxes(array $taxes): self
    {
        $this->taxes = $taxes;
        return $this;
    }


}