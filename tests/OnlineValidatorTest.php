<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\CFDI;

use Angle\CFDI\OnlineValidator;
use Angle\CFDI\XmlLoader;

use PHPUnit\Framework\TestCase;

final class OnlineValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $files = glob(__DIR__ . '/../test-data/*.xml', GLOB_ERR);

        print_r($files);

        $loader = new XmlLoader();

        foreach ($files as $filename) {
            $filename = realpath($filename);

            $cfdi = $loader->fileToCFDI($filename);

            if (!$cfdi) {
                // Loading failed!
                echo "FAILED" . PHP_EOL;
                print_r($loader->getErrors());
                print_r($loader->getValidations());

                $this->fail('CFDI could not be parsed from the XML file');
            }

            // Loading success!

            $this->assertInstanceOf(CFDI::class, $cfdi);


            $r = OnlineValidator::validate($cfdi);

            if ($r === OnlineValidator::RESULT_ERROR) {
                print_r(OnlineValidator::lastErrors());
                $this->fail('An error occurred on the OnlineValidator');
            }

            // Now check for a valid status!
            $this->assertEquals(OnlineValidator::RESULT_VALID, $r);

            echo PHP_EOL;
        }

        unset($loader);


    }
}