<?php

namespace Angle\CFDI;

use DOMDocument;
use DOMNode;

use Angle\CFDI\Invoice\Invoice;
use Angle\CFDI\Invoice\Issuer;
use Angle\CFDI\Invoice\Recipient;

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

        $invoice = Invoice::createFromDomNode($invoiceNode);

        if (!$invoiceNode->hasChildNodes()) {
            // at least one child node is required
            return null;
        }

        foreach ($invoiceNode->childNodes as $node) {
            echo $node->localName . PHP_EOL;

            /** @var DOMNode $node */

            switch ($node->localName) {
                case 'Emisor':
                    $issuer = Issuer::createFromDomNode($node);
                    $invoice->setIssuer($issuer);
                    break;
                case 'Receptor':
                    $recipient = Recipient::createFromDomNode($node);
                    $invoice->setRecipient($recipient);
                    break;
                default:
                //    throw new CFDIException(sprintf("Unknown node '%s'", $node->localName));
            }

        }

        return $invoice;
    }

}