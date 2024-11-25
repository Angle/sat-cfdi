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
 * @method static RelatedDocument createFromDOMNode(DOMNode $node)
 */
class RelatedDocument extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "DoctoRelacionado";
    public const NODE_NAME_EN = 'relatedDocument';

    const NODE_NS = "pago20";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];

    #########################
    ##     ATTRIBUTES      ##
    #########################

    public const ATTR_DOCUMENT_ID = 'id';
    public const ATTR_DOCUMENT_SERIES = 'series';
    public const ATTR_DOCUMENT_FOLIO = 'folio';
    public const ATTR_DOCUMENT_CURRENCY = 'currency';
    public const ATTR_DOCUMENT_EXCHANGE_RATE = 'exchangeRate';
    public const ATTR_DOCUMENT_INSTALLMENT_NUMBER = 'instalmentNumber';
    public const ATTR_DOCUMENT_PREVIOUS_BALANCE_AMOUNT = 'previousBalanceAmount';
    public const ATTR_DOCUMENT_PAID_AMOUNT = 'paidAmount';
    public const ATTR_DOCUMENT_OUTSTANDING_BALANCE_AMOUNT = 'pendingBalanceAmount';
    public const ATTR_OPERATION_TAXABLE = 'operationTaxable';

    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        self::ATTR_DOCUMENT_ID => [
            'keywords' => ['IdDocumento', self::ATTR_DOCUMENT_ID],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        self::ATTR_DOCUMENT_SERIES => [
            'keywords' => ['Serie', self::ATTR_DOCUMENT_SERIES],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_DOCUMENT_FOLIO => [
            'keywords' => ['Folio', self::ATTR_DOCUMENT_FOLIO],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_DOCUMENT_CURRENCY => [
            'keywords' => ['MonedaDR', self::ATTR_DOCUMENT_CURRENCY], // currency used in the referenced related document
            'type' => CFDINode::ATTR_REQUIRED
        ],
        self::ATTR_DOCUMENT_EXCHANGE_RATE => [
            'keywords' => ['EquivalenciaDR', self::ATTR_DOCUMENT_EXCHANGE_RATE],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_DOCUMENT_INSTALLMENT_NUMBER => [
            'keywords' => ['NumParcialidad', self::ATTR_DOCUMENT_INSTALLMENT_NUMBER],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_DOCUMENT_PREVIOUS_BALANCE_AMOUNT => [
            'keywords' => ['ImpSaldoAnt', self::ATTR_DOCUMENT_PREVIOUS_BALANCE_AMOUNT],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_DOCUMENT_PAID_AMOUNT => [
            'keywords' => ['ImpPagado', self::ATTR_DOCUMENT_PAID_AMOUNT],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_DOCUMENT_OUTSTANDING_BALANCE_AMOUNT => [
            'keywords' => ['ImpSaldoInsoluto', self::ATTR_DOCUMENT_OUTSTANDING_BALANCE_AMOUNT],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_OPERATION_TAXABLE => [
            'keywords' => ['ObjetoImpDR', self::ATTR_OPERATION_TAXABLE],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
    ];

    protected static $children = [
        'relatedDocumentTaxes' => [
            'keywords'  => ['ImpuestosDR', 'relatedDocumentTaxes'],
            'class'     => RelatedDocumentTaxes::class,
            'type'      => CFDINode::CHILD_UNIQUE,
        ],
    ];



    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string|null
     */
    protected $id;

    /**
     * @var string|null
     */
    protected $series;

    /**
     * @var string|null
     */
    protected $folio;

    /**
     * @var string|null
     */
    protected $currency;

    /**
     * @var string|null
     */
    protected $exchangeRate;

    /**
     * @var string|null
     */
    protected $instalmentNumber;

    /**
     * @var string|null
     */
    protected $previousBalanceAmount;

    /**
     * @var string|null
     */
    protected $paidAmount;

    /**
     * @var string|null
     */
    protected $pendingBalanceAmount;

    /**
     * @var string|null
     */
    protected $operationTaxable;

    // CHILDREN NODES
    /**
     * @var RelatedDocumentTaxes|null
     */
    protected $relatedDocumentTaxes;


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
                case RelatedDocumentTaxes::NODE_NAME:
                    $taxes = RelatedDocumentTaxes::createFromDOMNode($node);
                    $this->setRelatedDocumentTaxes($taxes);
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

        if ($this->relatedDocumentTaxes) {
            // relatedDocumentTaxes is optional, can be null
            $taxesNode = $this->relatedDocumentTaxes->toDOMElement($dom);
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
     * @return string|null
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * @param string|null $id
     * @return RelatedDocument
     */
    public function setId(?string $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @param string|null $series
     * @return RelatedDocument
     */
    public function setSeries(?string $series): self
    {
        $this->series = $series;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFolio(): ?string
    {
        return $this->folio;
    }

    /**
     * @param string|null $folio
     * @return RelatedDocument
     */
    public function setFolio(?string $folio): self
    {
        $this->folio = $folio;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string|null $currency
     * @return RelatedDocument
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = strtoupper($currency);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExchangeRate(): ?string
    {
        return $this->exchangeRate;
    }

    /**
     * @param string|null $exchangeRate
     * @return RelatedDocument
     */
    public function setExchangeRate(?string $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getInstalmentNumber(): ?string
    {
        return $this->instalmentNumber;
    }

    /**
     * @param string|null $instalmentNumber
     * @return RelatedDocument
     */
    public function setInstalmentNumber(?string $instalmentNumber): self
    {
        $this->instalmentNumber = $instalmentNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPreviousBalanceAmount(): ?string
    {
        return $this->previousBalanceAmount;
    }

    /**
     * @param string|null $previousBalanceAmount
     * @return RelatedDocument
     */
    public function setPreviousBalanceAmount(?string $previousBalanceAmount): self
    {
        $this->previousBalanceAmount = $previousBalanceAmount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaidAmount(): ?string
    {
        return $this->paidAmount;
    }

    /**
     * @param string|null $paidAmount
     * @return RelatedDocument
     */
    public function setPaidAmount(?string $paidAmount): self
    {
        $this->paidAmount = $paidAmount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPendingBalanceAmount(): ?string
    {
        return $this->pendingBalanceAmount;
    }

    /**
     * @param string|null $pendingBalanceAmount
     * @return RelatedDocument
     */
    public function setPendingBalanceAmount(?string $pendingBalanceAmount): self
    {
        $this->pendingBalanceAmount = $pendingBalanceAmount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOperationTaxable(): ?string
    {
        return $this->operationTaxable;
    }

    /**
     * @param string|null $operationTaxable
     */
    public function setOperationTaxable(?string $operationTaxable): void
    {
        $this->operationTaxable = $operationTaxable;
    }

    /**
     * @return RelatedDocumentTaxes|null
     */
    public function getRelatedDocumentTaxes(): ?RelatedDocumentTaxes
    {
        return $this->relatedDocumentTaxes;
    }

    /**
     * @param RelatedDocumentTaxes|null $relatedDocumentTaxes
     * @return $this
     */
    public function setRelatedDocumentTaxes(?RelatedDocumentTaxes $relatedDocumentTaxes): self
    {
        $this->relatedDocumentTaxes = $relatedDocumentTaxes;
        return $this;
    }
}