<?php

namespace Angle\CFDI\Node\Complement\Payment20;

use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use Angle\CFDI\Node\Complement\PaymentInterface;
use Angle\CFDI\Utility\Math;
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
    public const NODE_NAME_EN = 'payment';

    const NODE_NS = "pago20";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];

    #########################
    ##     ATTRIBUTES      ##
    #########################

    public const ATTR_DATE = 'date';
    public const ATTR_PAYMENT_METHOD = 'paymentMethod';
    public const ATTR_CURRENCY = 'currency';
    public const ATTR_EXCHANGE_RATE = 'exchangeRate';
    public const ATTR_AMOUNT = 'amount';
    public const ATTR_TRANSACTION_NUMBER = 'transactionNumber';
    public const ATTR_PAYER_BANK_RFC = 'payerBankRfc';
    public const ATTR_PAYER_BANK_NAME = 'payerBankName';
    public const ATTR_PAYER_BANK_ACCOUNT = 'payerAccount';
    public const ATTR_BENEFICIARY_BANK_RFC = 'beneficiaryBankRfc';
    public const ATTR_BENEFICIARY_BANK_ACCOUNT = 'beneficiaryAccount';
    public const ATTR_PAYMENT_CHAIN_TYPE = 'paymentChainType';
    public const ATTR_PAYMENT_CHAIN_CERT = 'paymentCertificate';
    public const ATTR_PAYMENT_CHAIN = 'paymentChain';
    public const ATTR_PAYMENT_CHAIN_SIGNATURE = 'paymentSignature';

    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        self::ATTR_DATE          => [
            'keywords' => ['FechaPago', self::ATTR_DATE],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        self::ATTR_PAYMENT_METHOD           => [
            'keywords' => ['FormaDePagoP', self::ATTR_PAYMENT_METHOD],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        self::ATTR_CURRENCY          => [
            'keywords' => ['MonedaP', self::ATTR_CURRENCY],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        self::ATTR_EXCHANGE_RATE           => [
            'keywords' => ['TipoCambioP', self::ATTR_EXCHANGE_RATE],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_AMOUNT          => [
            'keywords' => ['Monto', self::ATTR_AMOUNT],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        self::ATTR_TRANSACTION_NUMBER           => [
            'keywords' => ['NumOperacion', self::ATTR_TRANSACTION_NUMBER],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_PAYER_BANK_RFC          => [
            'keywords' => ['RfcEmisorCtaOrd', self::ATTR_PAYER_BANK_RFC],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_PAYER_BANK_NAME          => [
            'keywords' => ['NomBancoOrdExt', self::ATTR_PAYER_BANK_NAME],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_PAYER_BANK_ACCOUNT => [
            'keywords' => ['CtaOrdenante', self::ATTR_PAYER_BANK_ACCOUNT],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_BENEFICIARY_BANK_RFC => [
            'keywords' => ['RfcEmisorCtaBen', self::ATTR_BENEFICIARY_BANK_RFC],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_BENEFICIARY_BANK_ACCOUNT => [
            'keywords' => ['CtaBeneficiario', self::ATTR_BENEFICIARY_BANK_ACCOUNT],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_PAYMENT_CHAIN_TYPE => [
            'keywords' => ['TipoCadPago', self::ATTR_PAYMENT_CHAIN_TYPE],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_PAYMENT_CHAIN_CERT => [
            'keywords' => ['CertPago', self::ATTR_PAYMENT_CHAIN_CERT],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_PAYMENT_CHAIN => [
            'keywords' => ['CadPago', self::ATTR_PAYMENT_CHAIN],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        self::ATTR_PAYMENT_CHAIN_SIGNATURE => [
            'keywords' => ['SelloPago', self::ATTR_PAYMENT_CHAIN_SIGNATURE],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
    ];

    protected static $children = [
        'relatedDocument' => [
            'keywords'  => ['DoctoRelacionado', 'relatedDocument'],
            'class'     => RelatedDocument::class,
            'type'      => CFDINode::CHILD_ARRAY,
        ],
        'taxes' => [
            'keywords'  => ['ImpuestosP', 'taxes'],
            'class'     => Taxes::class,
            'type'      => CFDINode::CHILD_UNIQUE,
        ],
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
     * @var Taxes
     */
    protected $taxes = null;


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
        if($this->taxes) {
            $taxesNode = $this->taxes->toDOMElement($dom);
            $node->appendChild($taxesNode);
        }

        return $node;
    }

    #########################
    ##   SPECIAL METHODS   ##
    #########################

    /**
     * This method will calculate the total transferred and retained taxes for all related documents
     *
     * It will go through all the related documents and their related document taxes.
     * Then it will calculate the total amount of each taxes and will store it in the
     * $this->taxes variable.
     *
     * @return void
     * @throws CFDIException
     */
    public function calculatePaymentTaxes(): void
    {

        $transfers = [];
        $retentions = [];

        foreach ($this->getRelatedDocuments() as $document) {
            $relatedDocumentTaxes = $document->getRelatedDocumentTaxes();
            if ($relatedDocumentTaxes){
                $relatedDocumentRetainedList = $relatedDocumentTaxes->getRelatedDocumentTaxesRetainedList();
                if ($relatedDocumentRetainedList) {
                    foreach ($relatedDocumentRetainedList->getRelatedDocumentTaxesRetained() as $tax) {
                        $key = $tax->getTax();

                        if (!array_key_exists($key, $retentions)) {
                            $retentions[$key] = [
                                'tax' => $key,
                                'amount' => '0',
                            ];
                        }

                        $taxAmount = $tax->getAmount() ?? '0';
                        $retentions[$key]['amount'] = Math::add($retentions[$key]['amount'], $taxAmount);
                    }
                }
                $relatedDocumentTransferredList = $relatedDocumentTaxes->getRelatedDocumentTaxesTransferredList();
                if ($relatedDocumentTransferredList) {
                    foreach ($relatedDocumentTransferredList->getRelatedDocumentTaxesTransferred() as $tax) {
                        $key = $tax->getTax() . '-' . $tax->getFactorType() . '-' . $tax->getRate();

                        if (!array_key_exists($key, $transfers)) {
                            $transfers[$key] = [
                                'base'          => '0',
                                'tax'           => $tax->getTax(),
                                'factorType'    => $tax->getFactorType(),
                                'rate'          => $tax->getRate(),
                                'amount'        => '0',
                            ];
                        }
                        $taxAmount = $tax->getAmount() ?? '0';
                        $taxBase = $tax->getBase() ?? '0';
                        $transfers[$key]['base'] = Math::add($transfers[$key]['base'],$taxBase);
                        $transfers[$key]['amount'] = Math::add($transfers[$key]['amount'], $taxAmount);
                    }
                }
            }
        }

        $this->taxes = new Taxes([]);
        $transferredList = new TaxesTransferredList([]);
        foreach ($transfers as $k => $t) {
            $tax = new TaxesTransferred($t);
            $transferredList->addTransfer($tax);
        }
        $this->taxes->setTransferredList($transferredList);

        $retentionList = new TaxesRetainedList([]);
        foreach ($retentions as $k => $t) {
            $tax = new TaxesRetained($t);
            $retentionList->addRetention($tax);
        }
        $this->taxes->setRetainedList($retentionList);

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
            return $this;
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
     * @return Taxes
     */
    public function getTaxes(): Taxes
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
    public function setTaxes(Taxes $taxes): self
    {
        $this->taxes = $taxes;
        return $this;
    }

}