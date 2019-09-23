<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\Invoice\Invoice;
use Angle\CFDI\Parser;
use Angle\CFDI\XmlValidator;
use PHPUnit\Framework\TestCase;

final class ValidatorTest extends TestCase
{
    const PATH = '/Users/mundofr/GitHub/Angle/sat-cfdi'; // FIXME: dynamic paths

    public function testValidate(): void
    {
        Parser::$DIR = self::PATH;

        $xml = self::PATH . '/test-data/QCS931209G49-A-94231073.xml';

        try {
            $invoice = Parser::xmlFileToInvoice($xml);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertInstanceOf(Invoice::class, $invoice);
    }
}