<?php

namespace Angle\CFDI\Node\Complement\Payment;

use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use Angle\CFDI\Node\Complement\PaymentInterface;
use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Payment createFromDOMNode(DOMNode $node)
 */
class Payment extends CFDINode implements PaymentInterface
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Pago";

    const NODE_NS = "pago10";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'date'           => [
            'keywords' => ['FechaPago', 'date'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'paymentMethod'           => [
            'keywords' => ['FormaDePagoP', 'paymentMethod'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'currency'           => [
            'keywords' => ['MonedaP', 'currency'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'exchangeRate'           => [
            'keywords' => ['TipoCambioP', 'exchangeRate'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'amount'           => [
            'keywords' => ['Monto', 'amount'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'transactionNumber'           => [
            'keywords' => ['NumOperacion', 'transactionNumber'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'payerBankRfc'           => [
            'keywords' => ['RfcEmisorCtaOrd', 'payerBankRfc'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'payerBankName'           => [
            'keywords' => ['NomBancoOrdExt', 'payerBankName'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'payerAccount' => [
            'keywords' => ['CtaOrdenante', 'payerAccount'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'beneficiaryBankRfc' => [
            'keywords' => ['RfcEmisorCtaBen', 'beneficiaryBankRfc'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'beneficiaryAccount' => [
            'keywords' => ['CtaBeneficiario', 'beneficiaryAccount'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'paymentChainType' => [
            'keywords' => ['TipoCadPago', 'paymentChainType'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'paymentCertificate' => [
            'keywords' => ['CertPago', 'paymentCertificate'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'paymentChain' => [
            'keywords' => ['CadPago', 'paymentChain'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'paymentSignature' => [
            'keywords' => ['SelloPago', 'paymentSignature'],
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
     * @var DateTime
     */
    protected $date;

    /**
     * @var string|null
     */
    protected $paymentMethod;

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
    protected $amount;

    /**
     * @var string|null
     */
    protected $transactionNumber;

    /**
     * @var string|null
     */
    protected $payerBankRfc;

    /**
     * @var string|null
     */
    protected $payerBankName;

    /**
     * @var string|null
     */
    protected $payerAccount;

    /**
     * @var string|null
     */
    protected $beneficiaryBankRfc;

    /**
     * @var string|null
     */
    protected $beneficiaryAccount;

    /**
     * @var string|null
     */
    protected $paymentChainType;

    /**
     * @var string|null
     */
    protected $paymentCertificate;

    /**
     * @var string|null
     */
    protected $paymentChain;

    /**
     * @var string|null
     */
    protected $paymentSignature;


    // CHILDREN NODES
    /**
     * @var RelatedDocument[]
     */
    protected $documents = [];

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
                case RelatedDocument::NODE_NAME:
                    $document = RelatedDocument::createFromDomNode($node);
                    $this->addRelatedDocument($document);
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

        // Related Document Node
        foreach ($this->documents as $document) {
            $documentNode = $document->toDOMElement($dom);
            $node->appendChild($documentNode);
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
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|string $rawDate
     * @throws CFDIException
     * @return Payment
     */
    public function setDate($rawDate): self
    {
        if ($rawDate instanceof DateTime) {
            $this->date = $rawDate;
        }

        // sample format: 2019-09-06T10:09:46
        // TODO: We are assuming that dates ARE in Mexico City's timezone
        try {
            $tz = new DateTimeZone(CFDINode::DATETIME_TIMEZONE);
            $date = DateTime::createFromFormat(CFDINode::DATETIME_FORMAT, $rawDate, $tz);
        } catch (\Exception $e) {
            throw new CFDIException('Raw date string is in invalid format, cannot parse stamp date');
        }

        $this->date = $date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /**
     * @param string|null $paymentMethod
     * @return Payment
     */
    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
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
     * @return Payment
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
     * @return Payment
     */
    public function setExchangeRate(?string $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @param string|null $amount
     * @return Payment
     */
    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTransactionNumber(): ?string
    {
        return $this->transactionNumber;
    }

    /**
     * @param string|null $transactionNumber
     * @return Payment
     */
    public function setTransactionNumber(?string $transactionNumber): self
    {
        $this->transactionNumber = $transactionNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPayerBankRfc(): ?string
    {
        return $this->payerBankRfc;
    }

    /**
     * @param string|null $payerBankRfc
     * @return Payment
     */
    public function setPayerBankRfc(?string $payerBankRfc): self
    {
        $this->payerBankRfc = $payerBankRfc;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPayerBankName(): ?string
    {
        return $this->payerBankName;
    }

    /**
     * @param string|null $payerBankName
     * @return Payment
     */
    public function setPayerBankName(?string $payerBankName): self
    {
        $this->payerBankName = $payerBankName;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPayerAccount(): ?string
    {
        return $this->payerAccount;
    }

    /**
     * @param string|null $payerAccount
     * @return Payment
     */
    public function setPayerAccount(?string $payerAccount): self
    {
        $this->payerAccount = $payerAccount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBeneficiaryBankRfc(): ?string
    {
        return $this->beneficiaryBankRfc;
    }

    /**
     * @param string|null $beneficiaryBankRfc
     * @return Payment
     */
    public function setBeneficiaryBankRfc(?string $beneficiaryBankRfc): self
    {
        $this->beneficiaryBankRfc = $beneficiaryBankRfc;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBeneficiaryAccount(): ?string
    {
        return $this->beneficiaryAccount;
    }

    /**
     * @param string|null $beneficiaryAccount
     * @return Payment
     */
    public function setBeneficiaryAccount(?string $beneficiaryAccount): self
    {
        $this->beneficiaryAccount = $beneficiaryAccount;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentChainType(): ?string
    {
        return $this->paymentChainType;
    }

    /**
     * @param string|null $paymentChainType
     * @return Payment
     */
    public function setPaymentChainType(?string $paymentChainType): self
    {
        $this->paymentChainType = $paymentChainType;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentCertificate(): ?string
    {
        return $this->paymentCertificate;
    }

    /**
     * @param string|null $paymentCertificate
     * @return Payment
     */
    public function setPaymentCertificate(?string $paymentCertificate): self
    {
        $this->paymentCertificate = $paymentCertificate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentChain(): ?string
    {
        return $this->paymentChain;
    }

    /**
     * @param string|null $paymentChain
     * @return Payment
     */
    public function setPaymentChain(?string $paymentChain): self
    {
        $this->paymentChain = $paymentChain;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPaymentSignature(): ?string
    {
        return $this->paymentSignature;
    }

    /**
     * @param string|null $paymentSignature
     * @return Payment
     */
    public function setPaymentSignature(?string $paymentSignature): self
    {
        $this->paymentSignature = $paymentSignature;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return RelatedDocument[]
     */
    public function getRelatedDocuments(): ?array
    {
        return $this->documents;
    }

    /**
     * @param RelatedDocument $document
     * @return Payment
     */
    public function addRelatedDocument(RelatedDocument $document): self
    {
        $this->documents[] = $document;
        return $this;
    }

    /**
     * @param RelatedDocument[] $documents
     * @return Payment
     */
    public function setRelatedDocuments(array $documents): self
    {
        $this->documents = $documents;
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
     * @return Payment
     */
    public function addTaxes(Taxes $taxes): self
    {
        $this->taxes[] = $taxes;
        return $this;
    }

    /**
     * @param Taxes[] $taxes
     * @return Payment
     */
    public function setTaxes(array $taxes): self
    {
        $this->taxes = $taxes;
        return $this;
    }

}