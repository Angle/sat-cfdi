<?php

namespace Angle\CFDI\Node\Complement\HydrocarbonsAndPetroleum;

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
 * @method static HydrocarbonsAndPetroleum createFromDOMNode(DOMNode $node)
 */
class HydrocarbonsAndPetroleum extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION_1_0 = "1.0";

    const NODE_NAME = "HidroYPetro";

    const NODE_NS = "hidrocarburospetroliferos";
    const NODE_NS_URI = "http://www.sat.gob.mx/hidrocarburospetroliferos";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [
        'xmlns:hidrocarburospetroliferos' => "http://www.sat.gob.mx/hidrocarburospetroliferos",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/hidrocarburospetroliferos http://www.sat.gob.mx/sitio_internet/cfd/hidrocarburospetroliferos.xsd",
    ];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'version'           => [
            'keywords' => ['Version', 'version'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'permissionType'       => [
            'keywords' => ['TipoPermiso', 'permissionType'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'permissionNumber'               => [
            'keywords' => ['NumeroPermiso', 'permissionNumber'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'hypKey' => [
            'keywords' => ['ClaveHYP', 'hypKey'],
            'type' => CFDINode::ATTR_REQUIRED,
        ],
        'hypSubProduct' => [
            'keywords' => ['SubProductoHYP', 'hypSubProduct'],
            'type' => CFDINode::ATTR_REQUIRED,
        ],
    ];

    protected static $children = [];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $version = self::VERSION_1_0;

    /**
     * @var string
     */
    protected $permissionType;

    /**
     * @var string
     */
    protected $permissionNumber;

    /**
     * @var string
     */
    protected $hypKey;

    /**
     * @var string
     */
    protected $hypSubProduct;
    

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
     * @return HydrocarbonsAndPetroleum
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
    public function getPermissionType(): ?string
    {
        return $this->permissionType;
    }

    /**
     * @param string|null $permissionType
     * @return HydrocarbonsAndPetroleum
     */
    public function setPermissionType(?string $permissionType): self
    {
        $this->permissionType = $permissionType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPermissionNumber(): ?string
    {
        return $this->permissionNumber;
    }

    /**
     * @param string|null $permissionNumber
     * @return HydrocarbonsAndPetroleum
     */
    public function setPermissionNumber(?string $permissionNumber): self
    {
        $this->permissionNumber = $permissionNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getHypKey(): ?string
    {
        return $this->hypKey;
    }

    /**
     * @param string|null $hypKey
     * @return HydrocarbonsAndPetroleum
     */
    public function setHypKey(?string $hypKey): self
    {
        $this->hypKey = $hypKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getHypSubProduct(): ?string
    {
        return $this->hypSubProduct;
    }

    /**
     * @param string|null $hypSubProduct
     * @return HydrocarbonsAndPetroleum
     */
    public function setHypSubProduct(?string $hypSubProduct): self
    {
        $this->hypSubProduct = $hypSubProduct;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################
}
