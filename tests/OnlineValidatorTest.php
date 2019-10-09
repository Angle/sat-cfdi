<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\Invoice\Invoice;

use Angle\CFDI\OnlineValidator;
use Angle\CFDI\Parser;
use Angle\CFDI\XmlValidator;

use PHPUnit\Framework\TestCase;

final class OnlineValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $files = glob(__DIR__ . '/../test-data/*.xml', GLOB_ERR);

        print_r($files);

        foreach ($files as $filename) {
            $filename = realpath($filename);

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


            OnlineValidator::validate($invoice);

            echo PHP_EOL;
        }


    }
}