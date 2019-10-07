<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\Invoice\Invoice;
use Angle\CFDI\Parser;
use Angle\CFDI\XmlValidator;
use PHPUnit\Framework\TestCase;

final class ParserTest extends TestCase
{
    public function testValidate(): void
    {
        $xml = __DIR__ . '/../test-data/QCS931209G49-A-94231073.xml';

        echo "Source XML: " . PHP_EOL;
        echo file_get_contents($xml);
        echo PHP_EOL;

        try {
            $invoice = Parser::xmlFileToInvoice($xml);
        } catch (\Exception $e) {
            $this->fail($e->getMessage());
        }

        $this->assertInstanceOf(Invoice::class, $invoice);

        // Write out the XML, check if we match the same file
        print_r($invoice);
        echo "Result XML:" . PHP_EOL;
        echo $invoice->toXML();
    }
}