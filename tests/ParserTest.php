<?php

namespace Angle\CFDI\Tests;

use PHPUnit\Framework\TestCase;

use Angle\CFDI\CFDI;
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
            echo "#################################################################" . PHP_EOL;
            echo "## Source XML: " . $filename . PHP_EOL;
            echo "#################################################################" . PHP_EOL . PHP_EOL;


            echo "## Original XML ## " . PHP_EOL . PHP_EOL;
            echo file_get_contents($filename);
            echo PHP_EOL . PHP_EOL;

            $cfdi = $loader->fileToCFDI($filename);

            if (!$cfdi) {
                // Loading failed!
                echo "-> Parse FAILED" . PHP_EOL;

                echo "Errors:" . PHP_EOL;
                print_r($loader->getErrors());
                echo PHP_EOL;

                echo "Validations:" . PHP_EOL;
                print_r($loader->getValidations());
                echo PHP_EOL;

                $this->fail('CFDI could not be parsed from the XML file');
            }

            // Loading success!
            echo "-> Parse SUCCESS!!" . PHP_EOL;

            echo "Errors:" . PHP_EOL;
            print_r($loader->getErrors());
            echo PHP_EOL;

            echo "Validations:" . PHP_EOL;
            print_r($loader->getValidations());
            echo PHP_EOL;

            $this->assertInstanceOf(CFDI::class, $cfdi);

            echo "## Parsed CFDI Object ## " . PHP_EOL . PHP_EOL;
            print_r($cfdi);
            echo PHP_EOL . PHP_EOL;

            echo "## Output XML (reproduced) ## " . PHP_EOL . PHP_EOL;
            echo $cfdi->toXML();
            echo PHP_EOL . PHP_EOL;

        }

        unset($loader);
    }
}