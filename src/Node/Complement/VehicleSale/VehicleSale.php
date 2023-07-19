<?php

namespace Angle\CFDI\Node\Complement\VehicleSale;

use Angle\CFDI\CFDI33;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use Angle\CFDI\Node\Complement\VehicleSale\CustomsInformation;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static VehicleSale createFromDOMNode(DOMNode $node)
 */
class VehicleSale extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION_1_1 = "1.1";

    const NODE_NAME = "VentaVehiculos";

    const NODE_NS = "ventavehiculos";
    const NODE_NS_URI = "http://www.sat.gob.mx/ventavehiculos";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [
        'xmlns:ventavehiculos' => "http://www.sat.gob.mx/ventavehiculos",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/ventavehiculos https://www.sat.gob.mx/sitio_internet/cfd/ventavehiculos/ventavehiculos11.xsd",
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
        'vehicleCode'       => [
            'keywords' => ['ClaveVehicular', 'vehicleCode'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'vin'               => [
            'keywords' => ['Niv', 'vin'],
            'type' => CFDINode::ATTR_REQUIRED
        ]

    ];

    protected static $children = [
        // PropertyName => ClassName (full namespace)
        'customsInformation' => [
            'keywords'  => ['InformacionAduanera', 'customsInformation'],
            'class'     => CustomsInformation::class,
            'type'      => CFDINode::CHILD_ARRAY,
        ],
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
    protected $vehicleCode;

    /**
     * @var string
     */
    protected $vin;


    // CHILDREN NODES
    /**
     * @var CustomsInformation[]
     */
    protected $customsInformation = [];


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
                case CustomsInformation::NODE_NAME:
                    $customsInformation = CustomsInformation::createFromDomNode($node);
                    $this->addCustomsInformation($customsInformation);
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

        // CustomsInformation Node
        foreach ($this->customsInformation as $customsInformation) {
            $customsInformationNode = $customsInformation->toDOMElement($dom);
            $node->appendChild($customsInformationNode);
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
     * @param string|null $version
     * @return VehicleSale
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
    public function getVehicleCode(): ?string
    {
        return $this->vehicleCode;
    }

    /**
     * @param string|null $vehicleCode
     * @return VehicleSale
     */
    public function setVehicleCode(?string $vehicleCode): self
    {
        $this->vehicleCode = $vehicleCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getVin(): ?string
    {
        return $this->vin;
    }

    /**
     * @param string|null $vin
     * @return VehicleSale
     */
    public function setVin(?string $vin): self
    {
        $this->vin = $vin;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return CustomsInformation[]
     */
    public function getCustomInformation(): ?array
    {
        return $this->customsInformation;
    }

    /**
     * @param CustomsInformation $customsInformation
     * @return VehicleSale
     */
    public function addCustomsInformation(CustomsInformation $customsInformation): self
    {
        $this->customsInformation[] = $customsInformation;
        return $this;
    }

    /**
     * @param CustomsInformation[] $customsInformation
     * @return VehicleSale
     */
    public function setCustomsInformation(array $customsInformation): self
    {
        $this->customsInformation = $customsInformation;
        return $this;
    }



}