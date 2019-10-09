<?php

namespace Angle\CFDI;

use Angle\CFDI\Invoice\Invoice;

class OnlineValidator
{
    const ENDPOINT = 'https://verificacfdi.facturaelectronica.sat.gob.mx/default.aspx';
    const WS = 'https://consultaqr.facturaelectronica.sat.gob.mx/consultacfdiservice.svc';

    public static function validate(Invoice $invoice)
    {
        // Build the Query parameters
        $query = [
            'id' => '', // UUID
            're' => $invoice->getIssuer()->getRfc(),
            'rr' => $invoice->getRecipient()->getRfc(),
            'rt' => $invoice->getTotal(),
            'fe' => substr($invoice->getSignature(), -8),
        ];

        // build URL
        $url = self::ENDPOINT . http_build_query($query);

        echo 'URL: ' . $url . PHP_EOL;
        // TODO: do http request
    }
}