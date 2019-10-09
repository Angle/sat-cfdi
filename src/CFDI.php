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


    ## Helper methods, according to the spec

    public static function cleanWhitespace(string $s): string
    {
        // Replace all non visible characters with a single space
        $s = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $s);

        // Trim whitespace at the beginning and end of the string
        $s = trim($s);

        // Collapse multiple spaces into a single space
        $s = preg_replace('/\s+/u', ' ', $s);

        return $s;
    }
}