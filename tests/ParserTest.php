<?php

namespace Angle\CFDI\Tests;

use PHPUnit\Framework\TestCase;

use Angle\CFDI\Invoice\Invoice;
use Angle\CFDI\XmlLoader;


final class ParserTest extends TestCase
{
    public function testValidate(): void
    {
        $files = glob(__DIR__ . '/../test-data/*.xml', GLOB_ERR);

        $realFiles = [];
        foreach ($files as $filename) {
            $realFiles[] = realpath($filename);
        }

        print_r($realFiles);

        $loader = new XmlLoader();

        foreach ($realFiles as $filename) {
            echo "Source XML: " . $filename . PHP_EOL;
            echo file_get_contents($filename);
            echo PHP_EOL;

            $invoice = $loader->fileToInvoice($filename);

            if (!$invoice) {
                // Loading failed!
                echo "FAILED" . PHP_EOL;
                print_r($loader->getErrors());
                print_r($loader->getValidations());

                $this->fail('Invoice could not be parsed from the XML file');
            }

            // Loading success!
            echo "SUCCESS!!" . PHP_EOL;
            print_r($loader->getErrors());
            print_r($loader->getValidations());

            $this->assertInstanceOf(Invoice::class, $invoice);

            // Write out the XML, check if we match the same file
            print_r($invoice);

            echo serialize($invoice);

            echo "Result XML:" . PHP_EOL;
            echo $invoice->toXML();
            echo PHP_EOL;
            echo "UUID: " . $invoice->getUuid() . PHP_EOL . PHP_EOL;

            // STEP 1: Parse (XML to Invoice)
            // STEP 2: Validate properties
            // STEP 3: Validate signature
            // STEP 4: Validate Fiscal Stamp (TODO: should we check in here if the CFDI Signature matches the TFD?)
            // STEP 5: Validate UUID against SAT <- optional ?

        }

        unset($loader);
    }
}