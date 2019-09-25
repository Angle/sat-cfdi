<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\Invoice\Invoice;
use Angle\CFDI\Parser;
use Angle\CFDI\XmlValidator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $xml = __DIR__ . '/../test-data/QCS931209G49-A-94231073.xml';

        try {
            $invoice = Parser::xmlFileToInvoice($xml);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertInstanceOf(Invoice::class, $invoice);
    }
}