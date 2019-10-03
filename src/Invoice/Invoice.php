<?php

namespace Angle\CFDI\Invoice;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use DateTime;
use DateTimeZone;
use RuntimeException;

use DOMDocument;
use DOMElement;
use DOMNode;

class Invoice
{
    #########################
    ##        PRESETS      ##
    #########################

    const SERIES_WRONG_LENGTH_ERROR = 1;

    const DATETIME_FORMAT = 'Y-m-d\TH:i:s';
    const DATETIME_TIMEZONE = 'America/Mexico_City';


    #########################
    ##      PROPERTIES     ##
    #########################

    const NODE_NAME = 'cfdi:Comprobante';


    /**
     * Display Label: Version
     * Required
     * Fixed Value: "3.3"
     * No whitespace
     * @var string
     */
    protected $version = CFDI::VERSION;

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
     * @var DateTime
     */
    protected $date;

    protected $paymentMethod; // Forma de Pago

    protected $subTotal;

    protected $discount;

    protected $currency;

    protected $exchangeRate;

    protected $total;

    protected $invoiceType;

    protected $paymentType; // Método de Pago

    protected $postalCode;


    protected $signature;

    protected $certificateNumber;

    protected $certificate;

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


    #########################
    ##     CONSTRUCTOR     ##
    #########################

    /**
     * Invoice constructor.
     * @param array $data [$attributeName => $value]
     * @throws CFDIException
     */
    public function __construct(array $data)
    {
        // Lookup each element in the given array, attempt to find the corresponding property even if the input is in english or spanish
        foreach ($data as $key => $value) {
            // If the property is in the "base attributes" list, ignore it.
            if (array_key_exists($key, $this->baseAttributes())) {
                continue;
            }

            // Find the corresponding propertyName from the current attribute key
            $propertyName = $this->findPropertyName($key);

            if ($propertyName === null) {
                // Attribute name not found.
                throw new CFDIException("Invalid Attribute Name given, '$key' not found in Invoice object definition.", -1); // TODO: Pelos: add a proper code
            }

            $setter = 'set' . ucfirst($propertyName);
            if (!method_exists(self::class, $setter)) {
                throw new CFDIException("Property '$propertyName' has no setter method.", -1); // TODO: Pelos: add a proper code
            }


            // If the setter fails, it'll throw a CFDIException. We'll let it arise, the final library user should be the one catching and handling these type of exceptions.
            $this->$setter($value);
        }
    }

    /**
     * @param DOMNode $node
     * @return Invoice
     * @throws CFDIException
     */
    public static function createFromDomNode(DOMNode $node): self
    {
        // Extract invoice data
        $invoiceData = [];

        if ($node->hasAttributes()) {
            foreach ($node->attributes as $attr) {
                $invoiceData[$attr->nodeName] = $attr->nodeValue;
            }
        }

        //echo "Invoice data:" . PHP_EOL;
        //print_r($invoiceData);

        try {
            $invoice = new Invoice($invoiceData);
        } catch (CFDIException $e) {
            // TODO: handle this exception
            throw $e;
        }


        return $invoice;
    }


    #########################
    ## INVOICE TO DOM TRANSLATION
    #########################

    public function baseAttributes(): array
    {
        return [
            'xmlns:cfdi'            => 'http://www.sat.gob.mx/cfd/3',
            'xmlns:xsi'             => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation'    => 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd',
        ];
    }

    public function getAttributes(): array
    {
        // TODO: should _this_ function trigger the validation???
        if (!$this->validate()) {
            throw new CFDIException('Invoice is not validated, cannot pull attributes');
        }

        $attr = $this->baseAttributes();

        // FIXME: We could be pulling this automatically from the same translation array used to populate the new object.

        $attr['Version'] = $this->version;
        $attr['Serie'] = $this->series;
        $attr['Folio'] = $this->folio;
        $attr['Fecha'] = $this->date->format(self::DATETIME_FORMAT);
        $attr['FormaPago'] = $this->paymentMethod;
        $attr['SubTotal'] = $this->subTotal;
        $attr['Moneda'] = $this->currency;

        if ($this->exchangeRate) {
            $attr['TipoCambio'] = $this->exchangeRate;
        }

        $attr['Total'] = $this->total;
        $attr['TipoDeComprobante'] = $this->invoiceType;
        $attr['MetodoPago'] = $this->paymentType;
        $attr['LugarExpedicion'] = $this->postalCode;

        $attr['Sello'] = $this->signature;
        $attr['NoCertificado'] = $this->certificateNumber;
        $attr['Certificado'] = $this->certificate;

        return $attr;
    }


    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NODE_NAME);

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

    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string|null
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return Invoice
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeries(): string
    {
        return $this->series;
    }

    /**
     * @param string $series
     * @return Invoice
     * @throws CFDIException
     */
    public function setSeries(string $series): self
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
            $tz = new DateTimeZone(self::DATETIME_TIMEZONE);
            $date = DateTime::createFromFormat(self::DATETIME_FORMAT, $rawDate, $tz);
        } catch (\Exception $e) {
            throw new CFDIException('Raw date string is in invalid format, cannot parse date');
        }

        $this->date = $date;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod()
    {
        return $this->paymentMethod;
    }

    /**
     * @param mixed $paymentMethod
     * @return Invoice
     */
    public function setPaymentMethod($paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSubTotal()
    {
        return $this->subTotal;
    }

    /**
     * @param mixed $subTotal
     * @return Invoice
     */
    public function setSubTotal($subTotal): self
    {
        $this->subTotal = $subTotal;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param mixed $discount
     * @return Invoice
     */
    public function setDiscount($discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     * @return Invoice
     */
    public function setCurrency($currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getExchangeRate()
    {
        return $this->exchangeRate;
    }

    /**
     * @param mixed $exchangeRate
     * @return Invoice
     */
    public function setExchangeRate($exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * @param mixed $total
     * @return Invoice
     */
    public function setTotal($total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInvoiceType()
    {
        return $this->invoiceType;
    }

    /**
     * @param mixed $invoiceType
     * @return Invoice
     */
    public function setInvoiceType($invoiceType): self
    {
        $this->invoiceType = $invoiceType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPaymentType()
    {
        return $this->paymentType;
    }

    /**
     * @param mixed $paymentType
     * @return Invoice
     */
    public function setPaymentType($paymentType): self
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $postalCode
     * @return Invoice
     */
    public function setPostalCode($postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSignature()
    {
        return $this->signature;
    }

    /**
     * @param mixed $signature
     * @return Invoice
     */
    public function setSignature($signature): self
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCertificateNumber()
    {
        return $this->certificateNumber;
    }

    /**
     * @param mixed $certificateNumber
     * @return Invoice
     */
    public function setCertificateNumber($certificateNumber): self
    {
        $this->certificateNumber = $certificateNumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCertificate()
    {
        return $this->certificate;
    }

    /**
     * @param mixed $certificate
     * @return Invoice
     */
    public function setCertificate($certificate): self
    {
        $this->certificate = $certificate;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfirmation()
    {
        return $this->confirmation;
    }

    /**
     * @param mixed $confirmation
     * @return Invoice
     */
    public function setConfirmation($confirmation): self
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



    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    private static $translationMap = [
        // PropertyName => [spanish (official SAT), english]
        'version'           => ['Version', 'version'],
        'series'            => ['Serie', 'series'],
        'folio'             => ['Folio', 'folio'],
        'date'              => ['Fecha', 'date'],
        'paymentMethod'     => ['FormaPago', 'paymentMethod'],
        'subTotal'          => ['SubTotal', 'subTotal'],
        'currency'          => ['Moneda', 'currency'],
        'exchangeRate'      => ['TipoCambio', 'exchangeRate'],
        'total'             => ['Total', 'total'],
        'invoiceType'       => ['TipoDeComprobante', 'invoiceType'],
        'paymentType'       => ['MetodoPago', 'paymentType'],
        'postalCode'        => ['LugarExpedicion', 'postalCode'],
        'signature'         => ['Sello', 'signature'],
        'certificateNumber' => ['NoCertificado', 'certificateNumber'],
        'certificate'       => ['Certificado', 'certificate']

    ];

    private function findPropertyName($prop): ?string
    {
        foreach (self::$translationMap as $propertyName => $translations) {
            if (in_array($prop, $translations)) {
                return $propertyName;
            }
        }

        return null;
    }
}