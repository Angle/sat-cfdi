<?php

namespace Angle\CFDI;

use DOMDocument;

class CFDI
{
    #########################
    ##       CATALOG       ##
    #########################

    const VERSION = "3.3";
    const DATETIME_FORMAT = 'Y-m-d\TH:i:s';

    // Payment Method
    const PAYMENT_METHOD_CASH =     "01";
    const PAYMENT_METHOD_CHECK =    "02"; // Cheque nominativo
    // many many more..


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
    private $document;

    public function __construct()
    {
        $this->document = new DOMDocument('1.0', 'UTF-8');
        $this->document->preserveWhiteSpace = false;
    }

    public static function CreateFromData(array $data)
    {
        $cfdi = new self();

    }
}