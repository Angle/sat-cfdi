<?php

namespace Angle\CFDI\Tests;

use PHPUnit\Framework\TestCase;

final class InvoiceTest extends TestCase
{
    public function testInvoiceCreationEnglish(): void
    {
        $data = [
            'version' => '3.3',
            'series' => 'F',
            'folio' => '1234',
        ];

        try {
            $invoice = new \Angle\CFDI\Invoice\Invoice($data);
        } catch (\Angle\CFDI\CFDIException $c) {
            $this->fail($c->getMessage());
            return;
        }


        $dom = new \DOMDocument('1.0','UTF-8');
        $dom->preserveWhiteSpace = false;
        $invoiceNode = $invoice->toDOMElement($dom);
        $dom->appendChild($invoiceNode);
        $xml = $dom->saveXML();

        $expected = '<?xml/>';


        $this->assertEquals(
            $xml,
            $expected
        );
    }
    public function testInvoiceCreationSpanish(): void
    {
        $data = [
            'Version' => '3.3',
            'Serie' => 'F',
            'Folio' => '1234',
        ];

        try {
            $invoice = new \Angle\CFDI\Invoice\Invoice($data);
        } catch (\Angle\CFDI\CFDIException $c) {
            $this->fail($c->getMessage());
            return;
        }

        $dom = new \DOMDocument('1.0','UTF-8');
        $dom->preserveWhiteSpace = false;

        $invoiceNode = $invoice->toDOMElement($dom);
        $dom->appendChild($invoiceNode);
        $xml = $dom->saveXML();

        $expected = '<?xml/>';


        $this->assertEquals(
            $xml,
            $expected
        );
    }

}