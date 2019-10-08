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
        $files = glob(__DIR__ . '/../test-data/*.xml', GLOB_ERR);

        print_r($files);

        foreach ($files as $filename) {
            $filename = realpath($filename);
            echo "Source XML: " . $filename . PHP_EOL;
            echo file_get_contents($filename);
            echo PHP_EOL;

            try {
                $invoice = Parser::xmlFileToInvoice($filename);
            } catch (\Exception $e) {
                $this->fail($e->getMessage());
            }

            if ($invoice === null) {
                // The process failed
                $this->fail('XML did not validate');
            }

            $this->assertInstanceOf(Invoice::class, $invoice);

            // Write out the XML, check if we match the same file
            print_r($invoice);
            echo "Result XML:" . PHP_EOL;
            echo $invoice->toXML();
            echo PHP_EOL;
        }


    }
}