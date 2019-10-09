<?php

namespace Angle\CFDI;

use DOMDocument;
use DOMNode;

use Angle\CFDI\Invoice\Invoice;
use Angle\CFDI\Invoice\Node\Issuer;
use Angle\CFDI\Invoice\Node\Recipient;
use Angle\CFDI\Invoice\Node\ItemList;

class Parser
{
    public static function xmlStringToInvoice(string $xmlString): ?Invoice
    {
        $validator = new XmlValidator();

        try {
            $r = $validator->validateXmlString($xmlString);
        } catch (\Exception $e) {
            throw $e;
        }

        if (!$r) {
            $errors = implode(' || ', $validator->getErrors());
            throw new \Exception('XML did not validate. [' . $errors . ']');
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
            $errors = implode(' || ', $validator->getErrors());
            throw new \Exception('XML did not validate. [Exception: ' . $e->getMessage() . '] [' . $errors . ']');
        }

        if (!$r) {
            $errors = implode(' || ', $validator->getErrors());
            throw new \Exception('XML did not validate. [' . $errors . ']');
        }

        $dom = $validator->getDOM();

        return self::domToInvoice($dom);
    }

    public static function domToInvoice(DOMDocument $dom): ?Invoice
    {
        $invoiceNode = $dom->firstChild;

        $invoice = Invoice::createFromDomNode($invoiceNode);

        return $invoice;
    }

}