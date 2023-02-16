<?php

namespace Angle\CFDI\Node\Complement\FoodVouchers;

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
 * @method static FoodVouchers createFromDOMNode(DOMNode $node)
 */
class FoodVouchers extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION_1_0 = "1.0";
    const OPERATION_TYPE_ELECTRONIC_WALLET = "monedero electrónico";

    const NODE_NAME = "ValesDeDespensa";

    const NODE_NS = "valesdedespensa";
    const NODE_NS_URI = "http://www.sat.gob.mx/valesdedespensa";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [
        'xmlns:pago10' => "http://www.sat.gob.mx/valesdedespensa",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/valesdedespensa http://www.sat.gob.mx/sitio_internet/cfd/valesdedespensa/valesdedespensa.xsd",
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
        'operationType'           => [
            'keywords' => ['tipoOperacion', 'operationType'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'employerRegistration'           => [
            'keywords' => ['registroPatronal', 'employerRegistration'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'accountNumber'           => [
            'keywords' => ['numeroDeCuenta', 'accountNumber'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'total'           => [
            'keywords' => ['total', 'total'],
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

    /**
     * @var string
     */
    protected $operationType = self::OPERATION_TYPE_ELECTRONIC_WALLET;

    /**
     * @var string|null
     */
    protected $employerRegistration;

    /**
     * @var string
     */
    protected $accountNumber;

    /**
     * @var string
     */
    protected $total;


    // CHILDREN NODES

    /**
     * @var ItemList|null
     */
    protected $itemList = [];


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
                case ItemList::NODE_NAME:
                    $itemList = ItemList::createFromDomNode($node);
                    $this->setItemList($itemList);
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

        // ItemList Node
        if ($this->itemList) {
            // This can be null, no problem if not found
            $itemListNode = $this->itemList->toDOMElement($dom);
            $node->appendChild($itemListNode);
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
     * @return self
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
    public function getOperationType(): string
    {
        return $this->operationType;
    }

    /**
     * @param string $operationType
     * @return self
     */
    public function setOperationType(string $operationType): self
    {
        // Note: this value is fixed, it cannot be set or changed
        //$this->operationType = $operationType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEmployerRegistration(): ?string
    {
        return $this->employerRegistration;
    }

    /**
     * @param string|null $employerRegistration
     * @return self
     */
    public function setEmployerRegistration(?string $employerRegistration): self
    {
        $this->employerRegistration = $employerRegistration;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * @param string $accountNumber
     * @return self
     */
    public function setAccountNumber(string $accountNumber): self
    {
        $this->accountNumber = $accountNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param string $total
     * @return self
     */
    public function setTotal($total): self
    {
        $this->total = $total;
        return $this;
    }

    #########################
    ## CHILDREN
    #########################

    /**
     * @return ItemList
     */
    public function getItemList(): ?ItemList
    {
        return $this->itemList;
    }

    /**
     * @param ItemList $itemList
     * @return self
     */
    public function setItemList(?ItemList $itemList): self
    {
        $this->itemList = $itemList;
        return $this;
    }

}