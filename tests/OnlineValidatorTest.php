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


            $r = OnlineValidator::validate($invoice);

            if ($r === OnlineValidator::RESULT_ERROR) {
                print_r(OnlineValidator::lastErrors());
                $this->fail('An error occurred on the OnlineValidator');
            }

            // Now check for a valid status!
            $this->assertEquals(OnlineValidator::RESULT_VALID, $r);

            echo PHP_EOL;
        }


    }
}