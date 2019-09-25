<?php

namespace Angle\CFDI;

use DOMDocument;

use Angle\CFDI\Invoice\Invoice;

class Parser
{
    public static function xmlStringToInvoice(string $xmlString): ?Invoice
    {
        $validator = new XmlValidator();

        try {
            $r = $validator->validateXmlString($xmlString);
        } catch (\Exception $e) {
            return null;
        }

        if (!$r) {
            return null;
        }

        $dom = $validator->getDOM();

        return self::domToInvoice($dom);
    }
    public static function xmlFileToInvoice(string $xmlFilePath): ?Invoice
    {
        $validator = new XmlValidator();

        try {
            $r = $validator->validateXmlFile($xmlFilePath);
        } catch (\Exception $e) {
            return null;
        }

        if (!$r) {
            return null;
        }

        $dom = $validator->getDOM();

        return self::domToInvoice($dom);
    }

    public static function domToInvoice(DOMDocument $dom): ?Invoice
    {
        $invoiceNode = $dom->firstChild;

        /*
        if ($dom->getElementsByTagNameNS('cfdi', 'Comprobante')->count() != 1) {
            return null;
        }

        $invoiceNode = $dom->getElementsByTagName('Comprobante')->item(0);
        */

        // Extract invoice data
        $invoiceData = [];

        if ($invoiceNode->hasAttributes()) {
            echo "has attributes!" . PHP_EOL;
            foreach ($invoiceNode->attributes as $attr) {
                $invoiceData[$attr->nodeName] = $attr->nodeValue;
            }
        }

        echo "Invoice data:" . PHP_EOL;
        print_r($invoiceData);

        $invoice = new Invoice($invoiceData);

        return $invoice;
    }

}