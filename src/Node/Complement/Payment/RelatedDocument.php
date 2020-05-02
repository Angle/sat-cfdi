<?php

namespace Angle\CFDI\Node\Complement\Payment;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * @method static RelatedDocument createFromDOMNode(DOMNode $node)
 */
class RelatedDocument extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "DoctoRelacionado";

    const NODE_NS = "pago10";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'id' => [
            'keywords' => ['IdDocumento', 'id'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'series' => [
            'keywords' => ['Serie', 'series'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'folio' => [
            'keywords' => ['Folio', 'folio'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'currency' => [
            'keywords' => ['MonedaDR', 'currency'], // currency used in the referenced related document
            'type' => CFDI::ATTR_REQUIRED
        ],
        'exchangeRate' => [
            'keywords' => ['TipoCambioDR', 'exchangeRate'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'paymentType' => [
            'keywords' => ['MetodoDePagoDR', 'paymentType'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'instalmentNumber' => [
            'keywords' => ['NumParcialidad', 'instalmentNumber'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'previousBalanceAmount' => [
            'keywords' => ['ImpSaldoAnt', 'previousBalanceAmount'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'paidAmount' => [
            'keywords' => ['ImpPagado', 'paidAmount'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'pendingBalanceAmount' => [
            'keywords' => ['ImpSaldoInsoluto', 'pendingBalanceAmount'],
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
    protected $paymentType;

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

        // no child nodes

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
    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    /**
     * @param string|null $paymentType
     * @return RelatedDocument
     */
    public function setPaymentType(?string $paymentType): self
    {
        $this->paymentType = $paymentType;
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

}