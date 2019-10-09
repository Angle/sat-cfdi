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

        $realFiles = [];
        foreach ($files as $filename) {
            $realFiles[] = realpath($filename);
        }

        print_r($realFiles);

        foreach ($realFiles as $filename) {
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
            echo "UUID: " . $invoice->getUuid() . PHP_EOL . PHP_EOL;

            // STEP 1: Parse (XML to Invoice)
            // STEP 2: Validate properties
            // STEP 3: Validate signature
            // STEP 4: Validate Fiscal Stamp (TODO: should we check in here if the CFDI Signature matches the TFD?)
            // STEP 5: Validate UUID against SAT <- optional ?

            /*
            $r = $invoice->checkSignature();

            if ($r !== 0) {
                $this->fail(sprintf('CFDI Invoice Cryptographic Signature did not validate. [%s]', implode(' | ', $r) ));
            }
            */
            $fiscalStamp = $invoice->getFiscalStamp();

            if (!$fiscalStamp) {
                $this->fail('Missing FiscalStamp in Invoice');
            }

            $r = $fiscalStamp->checkSignature();
            if ($r !== 0) {
                $this->fail(sprintf('TFD FiscalStamp Cryptographic Signature did not validate. [%s]', implode(' | ', $r) ));
            }


        }


    }
}