<?php

namespace Angle\CFDI\Node\Complement\Payment20;

use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Totals createFromDOMNode(DOMNode $node)
 */
class Totals extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Totales";

    const NODE_NS = "pago20";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];

    #########################
    ##     ATTRIBUTES      ##
    #########################

    public const ATTR_TOTAL_RETAINED_IVA = 'TotalRetencionesIVA';
    public const ATTR_TOTAL_RETAINED_ISR = 'TotalRetencionesISR';
    public const ATTR_TOTAL_RETAINED_IEPS = 'TotalRetencionesIEPS';
    public const ATTR_TOTAL_TRANSFERRED_BASE_IVA16 = 'TotalTrasladosBaseIVA16';
    public const ATTR_TOTAL_TRANSFERRED_TAX_IVA16 = 'TotalTrasladosImpuestoIVA16';
    public const ATTR_TOTAL_TRANSFERRED_BASE_IVA8 = 'TotalTrasladosBaseIVA8';
    public const ATTR_TOTAL_TRANSFERRED_TAX_IVA8 = 'TotalTrasladosImpuestoIVA8';
    public const ATTR_TOTAL_TRANSFERRED_BASE_IVA0 = 'TotalTrasladosBaseIVA0';
    public const ATTR_TOTAL_TRANSFERRED_TAX_IVA0 = 'TotalTrasladosImpuestoIVA0';
    public const ATTR_TOTAL_TRANSFERRED_BASE_IVA_EXEMPT = 'TotalTrasladosBaseIVAExento';
    public const ATTR_TOTAL_PAYMENTS_AMOUNT = 'MontoTotalPagos';

    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'totalRetainedIva'           => [
            'keywords' => [self::ATTR_TOTAL_RETAINED_IVA, 'totalRetainedIva'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalRetainedIsr'           => [
            'keywords' => [self::ATTR_TOTAL_RETAINED_ISR, 'totalRetainedIsr'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalRetainedIeps'           => [
            'keywords' => [self::ATTR_TOTAL_RETAINED_IEPS, 'totalRetainedIeps'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalTransferredBaseIva16'           => [
            'keywords' => [self::ATTR_TOTAL_TRANSFERRED_BASE_IVA16, 'totalTransferredBaseIva16'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalTransferredTaxIva16'           => [
            'keywords' => [self::ATTR_TOTAL_TRANSFERRED_TAX_IVA16, 'totalTransferredTaxIva16'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalTransferredBaseIva8'           => [
            'keywords' => [self::ATTR_TOTAL_TRANSFERRED_BASE_IVA8, 'totalTransferredBaseIva8'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalTransferredTaxIva8'           => [
            'keywords' => [self::ATTR_TOTAL_TRANSFERRED_TAX_IVA8, 'totalTransferredTaxIva8'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalTransferredBaseIva0'           => [
            'keywords' => [self::ATTR_TOTAL_TRANSFERRED_BASE_IVA0, 'totalTransferredBaseIva0'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalTransferredTaxIva0'           => [
            'keywords' => [self::ATTR_TOTAL_TRANSFERRED_TAX_IVA0, 'totalTransferredTaxIva0'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalTransferredBaseIvaExempt'           => [
            'keywords' => [self::ATTR_TOTAL_TRANSFERRED_BASE_IVA_EXEMPT, 'totalTransferredBaseIvaExempt'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'totalPaymentsAmount'           => [
            'keywords' => [self::ATTR_TOTAL_PAYMENTS_AMOUNT, 'totalPaymentsAmount'],
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