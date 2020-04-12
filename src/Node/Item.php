<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Item createFromDOMNode(DOMNode $node)
 */
class Item extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Concepto";

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
            'type' => CFDI::ATTR_REQUIRED
        ],
        'id'          => [
            'keywords' => ['NoIdentificacion', 'id'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'quantity'        => [
            'keywords' => ['Cantidad', 'quantity'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'unitCode'        => [
            'keywords' => ['ClaveUnidad', 'unitCode'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'unit'        => [
            'keywords' => ['Unidad', 'unit'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'description'        => [
            'keywords' => ['Descripcion', 'description'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'unitPrice'        => [
            'keywords' => ['ValorUnitario', 'unitPrice'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'amount'        => [
            'keywords' => ['Importe', 'amount'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'discount'        => [
            'keywords' => ['Descuento', 'discount'],
            'type' => CFDI::ATTR_OPTIONAL
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
    protected $unitCode;

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

    /**
     * @var string
     */
    protected $discount;


    // CHILDREN NODES
    /**
     * @var ItemTaxes|null
     */
    protected $taxes;

    /**
     * @var ItemCustomsInformation[]
     */
    protected $customsInformation = [];

    /**
     * @var ItemPropertyTaxAccount|null
     */
    protected $propertyTaxAccount;

    /**
     * @var ItemComplement|null
     */
    protected $complements;

    /**
     * @var ItemPart[]
     */
    protected $parts = [];


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
                case ItemTaxes::NODE_NAME:
                    $taxes = ItemTaxes::createFromDOMNode($node);
                    $this->setTaxes($taxes);
                    break;
                case ItemPropertyTaxAccount::NODE_NAME:
                    $propertyTaxAccount = ItemPropertyTaxAccount::createFromDOMNode($node);
                    $this->setPropertyTaxAccount($propertyTaxAccount);
                    break;
                case ItemComplement::NODE_NAME:
                    $complement = ItemComplement::createFromDOMNode($node);
                    $this->setComplements($complement);
                    break;
                case ItemPart::NODE_NAME:
                    $part = ItemPart::createFromDOMNode($node);
                    $this->addPart($part);
                    break;
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

        if ($this->taxes) {
            // taxes is optional, can be null
            $taxesNode = $this->taxes->toDOMElement($dom);
            $node->appendChild($taxesNode);
        }

        // Custom Information node (array)
        foreach ($this->customsInformation as $customs) {
            $customsNode = $customs->toDOMElement($dom);
            $node->appendChild($customsNode);
        }

        if ($this->propertyTaxAccount) {
            // propertyTaxAccount is optional, can be null
            $propertyTaxAccountNode = $this->propertyTaxAccount->toDOMElement($dom);
            $node->appendChild($propertyTaxAccountNode);
        }

        if ($this->complements) {
            // propertyTaxAccount is optional, can be null
            $complementsNode = $this->complements->toDOMElement($dom);
            $node->appendChild($complementsNode);
        }

        // Item Part node (array)
        foreach ($this->parts as $part) {
            $partNode = $part->toDOMElement($dom);
            $node->appendChild($partNode);
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
    public function getUnitCode(): ?string
    {
        return $this->unitCode;
    }

    /**
     * @param string $unitCode
     * @return Item
     */
    public function setUnitCode(?string $unitCode): self
    {
        $this->unitCode = $unitCode;
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

    /**
     * @return string
     */
    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    /**
     * @param string $discount
     * @return Item
     */
    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return ItemTaxes|null
     */
    public function getTaxes(): ?ItemTaxes
    {
        return $this->taxes;
    }

    /**
     * @param ItemTaxes|null $taxes
     * @return Item
     */
    public function setTaxes(?ItemTaxes $taxes): self
    {
        $this->taxes = $taxes;
        return $this;
    }

    /**
     * @return ItemPropertyTaxAccount|null
     */
    public function getPropertyTaxAccount(): ?ItemPropertyTaxAccount
    {
        return $this->propertyTaxAccount;
    }

    /**
     * @param ItemPropertyTaxAccount|null $propertyTaxAccount
     * @return Item
     */
    public function setPropertyTaxAccount(?ItemPropertyTaxAccount $propertyTaxAccount): self
    {
        $this->propertyTaxAccount = $propertyTaxAccount;
        return $this;
    }

    /**
     * @return ItemComplement|null
     */
    public function getComplements(): ?ItemComplement
    {
        return $this->complements;
    }

    /**
     * @param ItemComplement|null $complements
     * @return Item
     */
    public function setComplements(?ItemComplement $complements): self
    {
        $this->complements = $complements;
        return $this;
    }

    /**
     * @return ItemPart[]
     */
    public function getParts(): ?array
    {
        return $this->parts;
    }

    /**
     * @param ItemPart $part
     * @return Item
     */
    public function addPart(ItemPart $part): self
    {
        $this->parts[] = $part;
        return $this;
    }

    /**
     * @param ItemPart[] $parts
     * @return Item
     */
    public function setPart(array $parts): self
    {
        $this->parts = $parts;
        return $this;
    }

    /**
     * @return ItemCustomsInformation[]
     */
    public function getCustomsInformation(): ?array
    {
        return $this->customsInformation;
    }

    /**
     * @param ItemCustomsInformation $customs
     * @return Item
     */
    public function addCustomsInformation(ItemCustomsInformation $customs): self
    {
        $this->customsInformation[] = $customs;
        return $this;
    }

    /**
     * @param ItemCustomsInformation[] $customs
     * @return Item
     */
    public function setCustomsInformation(array $customs): self
    {
        $this->customsInformation = $customs;
        return $this;
    }
}