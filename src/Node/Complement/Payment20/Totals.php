<?php

namespace Angle\CFDI\Node\Complement\Payment20;

use Angle\CFDI\CFDIException;
use Angle\CFDI\CFDINode;
use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * @method static Totals createFromDOMNode(DOMNode $node)
 */
class Totals extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    public const NODE_NAME = "Totales";
    public const NODE_NAME_EN = "totals";

    public const NODE_NS = "pago20";
    public const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    public const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    public const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;
    public const ATTR_TOTAL_RETAINED_IVA = 'totalRetainedIva';

    #########################
    ##     ATTRIBUTES      ##
    #########################
    public const ATTR_TOTAL_RETAINED_ISR = 'totalRetainedIsr';
    public const ATTR_TOTAL_RETAINED_IEPS = 'totalRetainedIeps';
    public const ATTR_TOTAL_TRANSFERRED_BASE_IVA16 = 'totalTransferredBaseIva16';
    public const ATTR_TOTAL_TRANSFERRED_TAX_IVA16 = 'totalTransferredTaxIva16';
    public const ATTR_TOTAL_TRANSFERRED_BASE_IVA8 = 'totalTransferredBaseIva8';
    public const ATTR_TOTAL_TRANSFERRED_TAX_IVA8 = 'totalTransferredTaxIva8';
    public const ATTR_TOTAL_TRANSFERRED_BASE_IVA0 = 'totalTransferredBaseIva0';
    public const ATTR_TOTAL_TRANSFERRED_TAX_IVA0 = 'totalTransferredTaxIva0';
    public const ATTR_TOTAL_TRANSFERRED_BASE_IVA_EXEMPT = 'totalTransferredBaseIvaExempt';
    public const ATTR_TOTAL_PAYMENTS_AMOUNT = 'totalPaymentsAmount';
    protected static $baseAttributes = [];

    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################
    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        self::ATTR_TOTAL_RETAINED_IVA => [
            'keywords' => ['TotalRetencionesIVA', self::ATTR_TOTAL_RETAINED_IVA],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_RETAINED_ISR => [
            'keywords' => ['TotalRetencionesISR', self::ATTR_TOTAL_RETAINED_ISR],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_RETAINED_IEPS => [
            'keywords' => ['TotalRetencionesIEPS', self::ATTR_TOTAL_RETAINED_IEPS],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_TRANSFERRED_BASE_IVA16 => [
            'keywords' => ['TotalTrasladosBaseIVA16', self::ATTR_TOTAL_TRANSFERRED_BASE_IVA16],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_TRANSFERRED_TAX_IVA16 => [
            'keywords' => ['TotalTrasladosImpuestoIVA16', self::ATTR_TOTAL_TRANSFERRED_TAX_IVA16],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_TRANSFERRED_BASE_IVA8 => [
            'keywords' => ['TotalTrasladosBaseIVA8', self::ATTR_TOTAL_TRANSFERRED_BASE_IVA8],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_TRANSFERRED_TAX_IVA8 => [
            'keywords' => ['TotalTrasladosImpuestoIVA8', self::ATTR_TOTAL_TRANSFERRED_TAX_IVA8],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_TRANSFERRED_BASE_IVA0 => [
            'keywords' => ['TotalTrasladosBaseIVA0', self::ATTR_TOTAL_TRANSFERRED_BASE_IVA0],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_TRANSFERRED_TAX_IVA0 => [
            'keywords' => ['TotalTrasladosImpuestoIVA0', self::ATTR_TOTAL_TRANSFERRED_TAX_IVA0],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_TRANSFERRED_BASE_IVA_EXEMPT => [
            'keywords' => ['TotalTrasladosBaseIVAExento', self::ATTR_TOTAL_TRANSFERRED_BASE_IVA_EXEMPT],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_TOTAL_PAYMENTS_AMOUNT => [
            'keywords' => ['MontoTotalPagos', self::ATTR_TOTAL_PAYMENTS_AMOUNT],
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
    protected $totalRetainedIva;

    /**
     * @var string|null
     */
    protected $totalRetainedIsr;

    /**
     * @var string|null
     */
    protected $totalRetainedIeps;

    /**
     * @var string|null
     */
    protected $totalTransferredBaseIva16;

    /**
     * @var string|null
     */
    protected $totalTransferredTaxIva16;

    /**
     * @var string|null
     */
    protected $totalTransferredBaseIva8;

    /**
     * @var string|null
     */
    protected $totalTransferredTaxIva8;

    /**
     * @var string|null
     */
    protected $totalTransferredBaseIva0;

    /**
     * @var string|null
     */
    protected $totalTransferredTaxIva0;

    /**
     * @var string|null
     */
    protected $totalTransferredBaseIvaExempt;

    /**
     * @var string|null
     */
    protected $totalPaymentsAmount;


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
    public function getTotalRetainedIva(): ?string
    {
        return $this->totalRetainedIva;
    }

    /**
     * @param string|null $totalRetainedIva
     */
    public function setTotalRetainedIva(?string $totalRetainedIva): void
    {
        $this->totalRetainedIva = $totalRetainedIva;
    }

    /**
     * @return string|null
     */
    public function getTotalRetainedIsr(): ?string
    {
        return $this->totalRetainedIsr;
    }

    /**
     * @param string|null $totalRetainedIsr
     */
    public function setTotalRetainedIsr(?string $totalRetainedIsr): void
    {
        $this->totalRetainedIsr = $totalRetainedIsr;
    }

    /**
     * @return string|null
     */
    public function getTotalRetainedIeps(): ?string
    {
        return $this->totalRetainedIeps;
    }

    /**
     * @param string|null $totalRetainedIeps
     */
    public function setTotalRetainedIeps(?string $totalRetainedIeps): void
    {
        $this->totalRetainedIeps = $totalRetainedIeps;
    }

    /**
     * @return string|null
     */
    public function getTotalTransferredBaseIva16(): ?string
    {
        return $this->totalTransferredBaseIva16;
    }

    /**
     * @param string|null $totalTransferredBaseIva16
     */
    public function setTotalTransferredBaseIva16(?string $totalTransferredBaseIva16): void
    {
        $this->totalTransferredBaseIva16 = $totalTransferredBaseIva16;
    }

    /**
     * @return string|null
     */
    public function getTotalTransferredTaxIva16(): ?string
    {
        return $this->totalTransferredTaxIva16;
    }

    /**
     * @param string|null $totalTransferredTaxIva16
     */
    public function setTotalTransferredTaxIva16(?string $totalTransferredTaxIva16): void
    {
        $this->totalTransferredTaxIva16 = $totalTransferredTaxIva16;
    }

    /**
     * @return string|null
     */
    public function getTotalTransferredBaseIva8(): ?string
    {
        return $this->totalTransferredBaseIva8;
    }

    /**
     * @param string|null $totalTransferredBaseIva8
     */
    public function setTotalTransferredBaseIva8(?string $totalTransferredBaseIva8): void
    {
        $this->totalTransferredBaseIva8 = $totalTransferredBaseIva8;
    }

    /**
     * @return string|null
     */
    public function getTotalTransferredTaxIva8(): ?string
    {
        return $this->totalTransferredTaxIva8;
    }

    /**
     * @param string|null $totalTransferredTaxIva8
     */
    public function setTotalTransferredTaxIva8(?string $totalTransferredTaxIva8): void
    {
        $this->totalTransferredTaxIva8 = $totalTransferredTaxIva8;
    }

    /**
     * @return string|null
     */
    public function getTotalTransferredBaseIva0(): ?string
    {
        return $this->totalTransferredBaseIva0;
    }

    /**
     * @param string|null $totalTransferredBaseIva0
     */
    public function setTotalTransferredBaseIva0(?string $totalTransferredBaseIva0): void
    {
        $this->totalTransferredBaseIva0 = $totalTransferredBaseIva0;
    }

    /**
     * @return string|null
     */
    public function getTotalTransferredTaxIva0(): ?string
    {
        return $this->totalTransferredTaxIva0;
    }

    /**
     * @param string|null $totalTransferredTaxIva0
     */
    public function setTotalTransferredTaxIva0(?string $totalTransferredTaxIva0): void
    {
        $this->totalTransferredTaxIva0 = $totalTransferredTaxIva0;
    }

    /**
     * @return string|null
     */
    public function getTotalTransferredBaseIvaExempt(): ?string
    {
        return $this->totalTransferredBaseIvaExempt;
    }

    /**
     * @param string|null $totalTransferredBaseIvaExempt
     */
    public function setTotalTransferredBaseIvaExempt(?string $totalTransferredBaseIvaExempt): void
    {
        $this->totalTransferredBaseIvaExempt = $totalTransferredBaseIvaExempt;
    }

    /**
     * @return string|null
     */
    public function getTotalPaymentsAmount(): ?string
    {
        return $this->totalPaymentsAmount;
    }

    /**
     * @param string|null $totalPaymentsAmount
     */
    public function setTotalPaymentsAmount(?string $totalPaymentsAmount): void
    {
        $this->totalPaymentsAmount = $totalPaymentsAmount;
    }


    #########################
    ## CHILDREN
    #########################

    // none

}