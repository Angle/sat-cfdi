<?php

namespace Angle\CFDI\Invoice;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\Invoice\Issuer;
use Angle\CFDI\Invoice\Recipient;

use DOMDocument;
use DOMElement;

class Invoice
{
    #########################
    ##        PRESETS      ##
    #########################

    // none
    const SERIES_WRONG_LENGTH_ERROR = 1;


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
     * @var string
     */
    protected $date;

    protected $paymentMethod; // Forma de Pago

    protected $paymentConditions;

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
    ##
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




    #########################
    ## XML DOM TRANSLATION
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
        $attr = $this->baseAttributes();

        // FIXME: We could be pulling this automatically from the same translation array used to populate the new object.

        $attr['Version'] = $this->version;
        $attr['Serie'] = $this->series;
        $attr['Folio'] = $this->folio;
        $attr['Fecha'] = $this->date;
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
    }


    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NODE_NAME);

        foreach ($this->getAttributes() as $attr => $value) {
            $node->setAttribute($attr, $value);
        }

        // Issuer Node
        $issuerNode = $this->issuer->toDOMElement($dom);
        $node->appendChild($issuerNode);

        // Recipient Node
        $recipientNode = $this->recipient->toDOMElement($dom);
        $node->appendChild($recipientNode);

        return $node;
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
     * @param string $serie
     * @return Invoice
     * @throws CFDIException
     */
    public function setSeries(string $serie): self
    {
        // Validate Length
        $l = strlen($serie);

        if ($l < 1 || $l > 25) {
            throw new CFDIException("Series contains wrong length.", self::SERIES_WRONG_LENGTH_ERROR);
        }

        // Validate contents
        if (preg_match('/^[a-zA-Z0-0]$/', $serie)) {

        }



        $this->serie = $serie;
        return $this;
    }

    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    private static $translationMap = [
        // PropertyName => [english, spanish (official SAT)]
        'version'           => ['version', 'Version'],
        'series'            => ['series', 'Serie'],
        'folio'             => ['folio', 'Folio'],
        'date'              => ['date', 'Fecha'],
        'paymentMethod'     => ['paymentMethod', 'FormaPago'],
        'subTotal'          => ['subTotal', 'SubTotal'],
        'currency'          => ['currency', 'Moneda'],
        'exchangeRate'      => ['exchangeRate', 'TipoCambio'],
        'total'             => ['total', 'Total'],
        'invoiceType'       => ['invoiceType', 'TipoDeComprobante'],
        'paymentType'       => ['paymentType', 'MetodoPago'],
        'postalCode'        => ['postalCode', 'LugarExpedicion'],
        'signature'         => ['signature', 'Sello'],
        'certificateNumber' => ['certificateNumber', 'NoCertificado'],
        'certificate'       => ['certificate', 'Certificado']

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