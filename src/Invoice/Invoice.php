<?php

namespace Angle\CFDI\Invoice;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\Invoice\Node\FiscalStamp;
use Angle\CFDI\Invoice\Node\Issuer;
use Angle\CFDI\Invoice\Node\Recipient;
use Angle\CFDI\Invoice\Node\ItemList;
use Angle\CFDI\Invoice\Node\Complement;

use Angle\CFDI\OpenSSLUtility;

use DateTime;
use DateTimeZone;
use RuntimeException;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Invoice createFromDOMNode(DOMNode $node)
 */
class Invoice extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = 'Comprobante';
    const NS_NODE_NAME = 'cfdi:Comprobante';

    const SERIES_WRONG_LENGTH_ERROR = 1;


    protected static $baseAttributes = [
        'xmlns:cfdi'            => 'http://www.sat.gob.mx/cfd/3',
        'xmlns:xsi'             => 'http://www.w3.org/2001/XMLSchema-instance',
        'xsi:schemaLocation'    => 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd',
    ];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'version'           => [
            'keywords' => ['Version', 'version'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'series'            => [
            'keywords' => ['Serie', 'series'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'folio'             => [
            'keywords' => ['Folio', 'folio'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'date'              => [
            'keywords' => ['Fecha', 'date'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'paymentMethod'     => [
            'keywords' => ['FormaPago', 'paymentMethod'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'paymentConditions'     => [
            'keywords' => ['CondicionesDePago', 'paymentConditions'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'subTotal'          => [
            'keywords' => ['SubTotal', 'subTotal'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'discount'          => [
            'keywords' => ['Descuento', 'discount'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'currency'          => [
            'keywords' => ['Moneda', 'currency'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'exchangeRate'      => [
            'keywords' => ['TipoCambio', 'exchangeRate'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'total'             => [
            'keywords' => ['Total', 'total'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'invoiceType'       => [
            'keywords' => ['TipoDeComprobante', 'invoiceType'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'paymentType'       => [
            'keywords' => ['MetodoPago', 'paymentType'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'postalCode'        => [
            'keywords' => ['LugarExpedicion', 'postalCode'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'signature'         => [
            'keywords' => ['Sello', 'signature'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'certificateNumber' => [
            'keywords' => ['NoCertificado', 'certificateNumber'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'certificate'       => [
            'keywords' => ['Certificado', 'certificate'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * Display Label: Version
     * Required
     * Fixed Value: "3.3"
     * No whitespace
     * @var string
     */
    protected $version = CFDI::VERSION_3_3;

    /**
     * Display Label: Serie
     * Optional
     * MinLength = 1, MaxLength = 25
     * XSD Pattern: [^|]{1,25}
     * RegExp = ^[a-zA-Z0-9]$
     * No whitespace
     * @var string|null
     */
    protected $series;

    /**
     * Display Label: Folio
     * Optional
     * MinLength = 1, MaxLength = 40
     * XSD Pattern: [^|]{1,40}
     * RegExp = ^[a-zA-Z0-0]$
     * No whitespace
     * @var string|null
     */
    protected $folio;

    /**
     * Display Label: Fecha
     * Required
     * XSD Pattern: ((19|20)[0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])
     * RegExp =
     * No whitespace
     * @var DateTime|null
     */
    protected $date;

    /**
     * @var string
     */
    protected $paymentMethod; // Forma de Pago

    /**
     * @var string
     */
    protected $subTotal;

    /**
     * @var string
     */
    protected $discount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $exchangeRate;

    /**
     * @var string
     */
    protected $total;


    /**
     * @var string
     */
    protected $invoiceType; // Tipo de Comprobante

    /**
     * @var string
     */
    protected $paymentType; // Método de Pago

    /**
     * @var string
     */
    protected $paymentConditions; // Condiciones de Pago

    /**
     * @var string
     */
    protected $postalCode; // Lugar de Expedición


    /**
     * @var string
     */
    protected $signature; // Sello

    /**
     * @var string
     */
    protected $certificateNumber;

    /**
     * @var string
     */
    protected $certificate; // (public key)

    /**
     * @var string
     */
    protected $confirmation;



    // CHILDREN NODES
    /**
     * @var Issuer
     */
    protected $issuer;

    /**
     * @var Recipient
     */
    protected $recipient;

    /**
     * @var ItemList
     */
    protected $itemList;

    /**
     * @var Complement[]
     */
    protected $complements = [];


    #########################
    ##     CONSTRUCTOR     ##
    #########################

    // constructor implemented in the CFDINode abstract class

    /**
     * @param DOMNode[]
     * @throws CFDIException
     */
    public function setChildren(array $children): void
    {
        foreach ($children as $node) {
            if ($node instanceof DOMText) {
                // TODO: we are skipping the actual text inside the Node.. is this useful?
                // TODO: DOMText
                continue;
            }

            switch ($node->localName) {
                case Issuer::NODE_NAME:
                    $issuer = Issuer::createFromDOMNode($node);
                    $this->setIssuer($issuer);
                    break;
                case Recipient::NODE_NAME:
                    $recipient = Recipient::createFromDOMNode($node);
                    $this->setRecipient($recipient);
                    break;
                case ItemList::NODE_NAME:
                    $itemList = ItemList::createFromDOMNode($node);
                    $this->setItemList($itemList);
                    break;
                case Complement::NODE_NAME:
                    $complement = Complement::createFromDomNode($node);
                    $this->addComplement($complement);
                    break;
                default:
                    //throw new CFDIException(sprintf("Unknown children node '%s' in %s", $node->localName, self::NODE_NAME));
            }
        }
    }


    #########################
    ## INVOICE TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NS_NODE_NAME);

        foreach ($this->getAttributes() as $attr => $value) {
            $node->setAttribute($attr, $value);
        }

        // Issuer Node
        if ($this->issuer) {
            // TODO: What happens if the issuer is not set?
            $issuerNode = $this->issuer->toDOMElement($dom);
            $node->appendChild($issuerNode);
        }

        // Recipient Node
        if ($this->recipient) {
            // TODO: What happens if the recipient is not set?
            $recipientNode = $this->recipient->toDOMElement($dom);
            $node->appendChild($recipientNode);
        }

        // ItemList Node
        if ($this->itemList) {
            // TODO: What happens if the itemList is not set?
            $itemListNode = $this->itemList->toDOMElement($dom);
            $node->appendChild($itemListNode);
        }

        // Complements Node
        foreach ($this->complements as $complement) {
            $complementNode = $complement->toDOMElement($dom);
            $node->appendChild($complementNode);
        }

        return $node;
    }


    #########################
    ## INVOICE TO XML
    #########################

    public function toDOMDocument(): DOMDocument
    {
        $dom = new \DOMDocument('1.0','UTF-8');
        $dom->preserveWhiteSpace = false;

        $invoiceNode = $this->toDOMElement($dom);
        $dom->appendChild($invoiceNode);

        return $dom;
    }

    public function toXML(): string
    {
        return $this->toDOMDocument()->saveXML();
    }


    #########################
    ## VALIDATION
    #########################

    public function validate(): bool
    {
        // TODO: implement the full set of validation, including type and Business Logic

        if (!($this->date instanceof DateTime)) {
            return false;
        }

        return true;
    }

    public function getChainSequence(): string
    {
        return '';
    }

    /**
     * On success, returns 0
     * On failure, returns an array with any validation errors encountered.
     * @return array|0
     */
    public function checkSignature()
    {
        if ($this->version != CFDI::VERSION_3_3) {
            return ['Invoice Signature check is only implemented for CFDI v3.3'];
        }

        /////////
        // VALIDATE THE CERTIFICATE

        if (!$this->certificate) {
            return ['CFDI does not have an Issuer Certificate'];
        }

        $certificatePem = OpenSSLUtility::coerceBase64Certificate($this->certificate);

        $certificate = openssl_x509_read($certificatePem);

        if ($certificate === false) {
            return ['Certificate X.509 read failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        }

        $parsedCertificate = openssl_x509_parse($certificate);

        // Check that the Certificate matches the CFDI Issuer's RFC
        if (!array_key_exists('subject', $parsedCertificate)
            || !array_key_exists('x500UniqueIdentifier', $parsedCertificate['subject'])
            || !$parsedCertificate['subject']['x500UniqueIdentifier']) {
            return ['Certificate X.509 does not have a valid Subject x500UniqueIdentifier'];
        }

        // Extract and clean up the RFC
        $issuerRfc = explode('/', $parsedCertificate['subject']['x500UniqueIdentifier']);
        $issuerRfc = trim($issuerRfc[0]);

        if (!$this->issuer || !($this->issuer instanceof Issuer)) {
            return ['CFDI does not have an Issuer'];
        }

        if ($this->issuer->getRfc() != $issuerRfc) {
            return ['CFDI Issuer\'s RFC does not match certificate'];
        }


        // Check the certificate's CA
        $auth = openssl_x509_checkpurpose($certificate, X509_PURPOSE_ANY, [OpenSSLUtility::TRUSTED_CA_PEM]);
        if ($auth === false) {
            return ['Certificate not authentic: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        } elseif ($auth === -1) {
            return ['Certificate authenticity check failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        }


        ////////////////
        /// VALIDATE THE SIGNATURE

        if (!$this->signature) {
            return ['CFDI does not have a Signature'];
        }

        $signature = base64_decode($this->signature, true);

        if ($signature === false) {
            return ['Cannot decode CFDI signature'];
        }

        // Build the Original Chain Sequence
        // TODO: Check if the "original chain sequence" is properly built and compare it against the signature
        $chain = $this->getChainSequence();

        $publicKey = openssl_pkey_get_public($certificate);

        echo "Public Key: " . var_dump($publicKey);

        if ($publicKey === false) {
            return ['Public Key extraction failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        }

        // Verify the given signature with the Chain
        // Returns 1 if the signature is correct, 0 if it is incorrect, and -1 on error.
        $r = openssl_verify($chain, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        if ($r === 0) {
            return ['Signature is incorrect: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        } elseif ($r === -1) {
            return ['Signature verification failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        }


        // Validate against the Fiscal Stamp
        $fiscalStamp = $this->getFiscalStamp();
        if ($fiscalStamp === null) {
            // Fiscal stamp is not set
            return ['Fiscal Stamp is not set in Invoice'];
        }

        // The CFDI signature should be exactly the same as the one in the FiscalStamp node
        if ($this->signature !== $fiscalStamp->getCfdiSignature()) {
            // CFDI Signature mismatched
            return ['CFDI Signature mismatched in Fiscal Stamp'];
        }



        return 0;
    }



    #########################
    ##   SPECIAL METHODS   ##
    #########################

    /**
     * This will return the first UUID found in the Complements
     */
    public function getUuid(): ?string
    {
        foreach ($this->complements as $complement) {
            if ($complement->getFiscalStamp()) {
                if ($complement->getFiscalStamp()->getUuid()) {
                    return $complement->getFiscalStamp()->getUuid();
                }
            }
        }

        // nothing found
        return null;
    }

    /**
     * This will return the first FiscalStamp found in the Complements
     */
    public function getFiscalStamp(): ?FiscalStamp
    {
        foreach ($this->complements as $complement) {
            if ($complement->getFiscalStamp() instanceof FiscalStamp) {
                return $complement->getFiscalStamp();
            }
        }

        // nothing found
        return null;
    }

    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return Invoice
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
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @param string $series
     * @return Invoice
     * @throws CFDIException
     */
    public function setSeries(?string $series): self
    {
        // Validate Length
        $l = strlen($series);

        if ($l < 1 || $l > 25) {
            throw new CFDIException("Series contains wrong length.", self::SERIES_WRONG_LENGTH_ERROR);
        }

        // Validate contents
        if (preg_match('/^[a-zA-Z0-0]$/', $series)) {

        }



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
     * @return Invoice
     */
    public function setFolio(?string $folio): self
    {
        $this->folio = $folio;
        return $this;
    }


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
     * @return Invoice
     */
    public function setDate($rawDate): self
    {
        if ($rawDate instanceof DateTime) {
            $this->date = $rawDate;
        }

        // sample format: 2019-09-06T10:09:46
        // TODO: We are assuming that dates ARE in Mexico City's timezone
        try {
            $tz = new DateTimeZone(CFDI::DATETIME_TIMEZONE);
            $date = DateTime::createFromFormat(CFDI::DATETIME_FORMAT, $rawDate, $tz);
        } catch (\Exception $e) {
            throw new CFDIException('Raw date string is in invalid format, cannot parse date');
        }

        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return Invoice
     */
    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubTotal(): ?string
    {
        return $this->subTotal;
    }

    /**
     * @param string $subTotal
     * @return Invoice
     */
    public function setSubTotal(?string $subTotal): self
    {
        $this->subTotal = $subTotal;
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
     * @return Invoice
     */
    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return Invoice
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getExchangeRate(): ?string
    {
        return $this->exchangeRate;
    }

    /**
     * @param string $exchangeRate
     * @return Invoice
     */
    public function setExchangeRate(?string $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    /**
     * @return string
     */
    public function getTotal(): ?string
    {
        return $this->total;
    }

    /**
     * @param string $total
     * @return Invoice
     */
    public function setTotal(?string $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceType(): ?string
    {
        return $this->invoiceType;
    }

    /**
     * @param string $invoiceType
     * @return Invoice
     */
    public function setInvoiceType(?string $invoiceType): self
    {
        $this->invoiceType = $invoiceType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     * @return Invoice
     */
    public function setPaymentType(?string $paymentType): self
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentConditions(): ?string
    {
        return $this->paymentConditions;
    }

    /**
     * @param string $paymentConditions
     * @return Invoice
     */
    public function setPaymentConditions(?string $paymentConditions): self
    {
        $this->paymentConditions = $paymentConditions;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return Invoice
     */
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     * @return Invoice
     */
    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertificateNumber(): ?string
    {
        return $this->certificateNumber;
    }

    /**
     * @param string $certificateNumber
     * @return Invoice
     */
    public function setCertificateNumber(?string $certificateNumber): self
    {
        $this->certificateNumber = $certificateNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertificate(): ?string
    {
        return $this->certificate;
    }

    /**
     * @param string $certificate
     * @return Invoice
     */
    public function setCertificate(?string $certificate): self
    {
        $this->certificate = $certificate;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmation(): ?string
    {
        return $this->confirmation;
    }

    /**
     * @param string $confirmation
     * @return Invoice
     */
    public function setConfirmation(?string $confirmation): self
    {
        $this->confirmation = $confirmation;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return Issuer
     */
    public function getIssuer(): ?Issuer
    {
        return $this->issuer;
    }

    /**
     * @param Issuer $issuer
     * @return Invoice
     */
    public function setIssuer(Issuer $issuer): self
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * @return Recipient
     */
    public function getRecipient(): ?Recipient
    {
        return $this->recipient;
    }

    /**
     * @param Recipient $recipient
     * @return Invoice
     */
    public function setRecipient(Recipient $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return ItemList
     */
    public function getItemList(): ?ItemList
    {
        return $this->itemList;
    }

    /**
     * @param ItemList $itemList
     * @return Invoice
     */
    public function setItemList(ItemList $itemList): self
    {
        $this->itemList = $itemList;
        return $this;
    }

    /**
     * @return Complement[]
     */
    public function getComplements(): ?array
    {
        return $this->complements;
    }

    /**
     * @param Complement $complement
     * @return Invoice
     */
    public function addComplement(Complement $complement): self
    {
        $this->complements[] = $complement;
        return $this;
    }

    /**
     * @param Complement[] $complements
     * @return Invoice
     */
    public function setComplements(array $complements): self
    {
        $this->complements = $complements;
        return $this;
    }

}