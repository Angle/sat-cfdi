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
        }


        $expected = '<?xml/>';


        $this->assertEquals(
            \Angle\CFDI\Invoice\Invoice::class,
            $expected
        );
    }
    public function testInvoiceCreationSpanish(): void
    {
    }

}