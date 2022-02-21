<?php

namespace Angle\CFDI\Node\CFDI40;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIException;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static ItemPart createFromDOMNode(DOMNode $node)
 */
class ItemPart extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Parte";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'code'           => [
            'keywords' => ['ClaveProdServ', 'code'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'id'          => [
            'keywords' => ['NoIdentificacion', 'id'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'quantity'        => [
            'keywords' => ['Cantidad', 'quantity'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'unit'        => [
            'keywords' => ['Unidad', 'unit'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'description'        => [
            'keywords' => ['Descripcion', 'description'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'unitPrice'        => [
            'keywords' => ['ValorUnitario', 'unitPrice'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'amount'        => [
            'keywords' => ['Importe', 'amount'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
    ];

    protected static $children = [
        'customsInformation' => [
            'keywords'  => ['InformacionAduanera', 'customsInformation'],
            'class'     => ItemCustomsInformation::class,
            'type'      => CFDINode::CHILD_ARRAY,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $code;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $quantity;

    /**
     * @var string
     */
    protected $unit;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $unitPrice;

    /**
     * @var string
     */
    protected $amount;


    // CHILDREN NODES
    /**
     * @var ItemCustomsInformation[]
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
                case ItemCustomsInformation::NODE_NAME:
                    $customs = ItemCustomsInformation::createFromDOMNode($node);
                    $this->addCustomsInformation($customs);
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

        // Custom Information node (array)
        foreach ($this->customsInformation as $customs) {
            $customsNode = $customs->toDOMElement($dom);
            $node->appendChild($customsNode);
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
    public function getCode(): ?string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Item
     */
    public function setCode(?string $code): self
    {
        $this->code = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string $id
     * @return Item
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    /**
     * @param string $quantity
     * @return Item
     */
    public function setQuantity(?string $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit(): ?string
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return Item
     */
    public function setUnit(?string $unit): self
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Item
     */
    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnitPrice(): ?string
    {
        return $this->unitPrice;
    }

    /**
     * @param string $unitPrice
     * @return Item
     */
    public function setUnitPrice(?string $unitPrice): self
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return Item
     */
    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return ItemCustomsInformation[]
     */
    public function getCustomsInformation(): ?array
    {
        return $this->customsInformation;
    }

    /**
     * @param ItemCustomsInformation $customs
     * @return ItemPart
     */
    public function addCustomsInformation(ItemCustomsInformation $customs): self
    {
        $this->customsInformation[] = $customs;
        return $this;
    }

    /**
     * @param ItemCustomsInformation[] $customs
     * @return ItemPart
     */
    public function setCustomsInformation(array $customs): self
    {
        $this->customsInformation = $customs;
        return $this;
    }
}