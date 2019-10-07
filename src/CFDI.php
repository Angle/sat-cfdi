<?php

namespace Angle\CFDI;

use DOMDocument;

use Angle\CFDI\Invoice\Invoice;

class CFDI
{
    #########################
    ##       CATALOG       ##
    #########################

    const VERSION = "3.3";
    const DATETIME_FORMAT = 'Y-m-d\TH:i:s';
    const DATETIME_TIMEZONE = 'America/Mexico_City';

    // Payment Method
    const PAYMENT_METHOD_CASH =     "01";
    const PAYMENT_METHOD_CHECK =    "02"; // Cheque nominativo
    // many many more..

    const ATTR_REQUIRED = 'R';
    const ATTR_OPTIONAL = 'O';


    // Currency
    const CURRENCY_MXN = "MXN";
    const CURRENCY_USD = "USD";

    // Payment Type
    const PAYMENT_TYPE_SINGLE = "PUE"; // Pago en una sola exhibición
    const PAYMENT_TYPE_PARTIAL = "PPD"; // Pago en parcialidades o diferido



    ## Properties
    /**
     * @var DOMDocument
     */
    protected $document;

    /**
     * @var Invoice
     */
    protected $invoice;

    public function __construct()
    {

    }

    public function toDOMDocument(): DOMDocument
    {
        $this->document = new DOMDocument('1.0', 'UTF-8');
        $this->document->preserveWhiteSpace = false;

        $invoiceNode = $this->invoice->toDOMElement($this->document);
        $this->document->appendChild($invoiceNode);

        return $this->document;
    }

    public function toXML(): string
    {
        return $this->toDOMDocument()->saveXML();
    }



    public static function CreateFromData(array $data)
    {
        $cfdi = new self();

    }
}