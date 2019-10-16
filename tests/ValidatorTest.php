<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\CertificateStorage\LocalCertificateStorage;
use Angle\CFDI\CertificateStorage\OnlineCertificateStorage;
use Angle\CFDI\OnlineValidator;
use Angle\CFDI\SignatureValidator;
use Angle\CFDI\Utility\PathUtility;
use PHPUnit\Framework\TestCase;

use Angle\CFDI\XmlLoader;
use Angle\CFDI\CFDI;

final class ValidatorTest extends TestCase
{
    public function testValidate(): void
    {
        $files = glob(__DIR__ . '/../test-data/*.xml', GLOB_ERR);

        $realFiles = [];
        foreach ($files as $filename) {
            $realFiles[] = realpath($filename);
        }

        print_r($realFiles);

        // Initialize the XML Loader
        $loader = new XmlLoader();

        // Initialize the CertificateStorage as local and configure it for testing
        $storage = new LocalCertificateStorage(
            PathUtility::join(__DIR__, '/../resources/certificates/csd-sat/')
        );

        $storage = new OnlineCertificateStorage();

        $signatureValidator = new SignatureValidator($storage);

        foreach ($realFiles as $filename) {

            echo "#################################################################" . PHP_EOL;
            echo "## Source XML: " . $filename . PHP_EOL;
            echo "#################################################################" . PHP_EOL . PHP_EOL;

            // STEP 1: Parse (XML to Invoice)
            // STEP 2: Validate properties
            // STEP 3: Validate CFDI signature
            // STEP 4: Validate Fiscal Stamp
            // STEP 5: Validate UUID against SAT

            $errors = [];
            $validations = [];

            $cfdi = $loader->fileToCFDI($filename);

            $validations = array_merge($validations, $loader->getValidations());

            if (!$cfdi) {
                echo "Validations:" . PHP_EOL;
                print_r($validations);
                echo PHP_EOL;
                $this->fail('CFDI could not be parsed from the XML file. Please run ParserTest to debug.');
            }

            $this->assertInstanceOf(CFDI::class, $cfdi);


            echo PHP_EOL;
            echo "UUID: " . $cfdi->getUuid() . PHP_EOL . PHP_EOL;


            $r = $signatureValidator->checkCfdiSignature($cfdi);
            $errors = array_merge($errors, $signatureValidator->getErrors());
            $validations = array_merge($validations, $signatureValidator->getValidations());

            if ($r === false) {
                echo "Errors:" . PHP_EOL;
                print_r($errors);
                echo PHP_EOL;

                echo "Validations:" . PHP_EOL;
                print_r($validations);
                echo PHP_EOL;

                $this->fail('CFDI signature failed.');
            }

            $this->assertEquals(true, $r);



            $r = $signatureValidator->checkFiscalStampSignature($cfdi);
            $errors = array_merge($errors, $signatureValidator->getErrors());
            $validations = array_merge($validations, $signatureValidator->getValidations());

            if ($r === false) {
                echo "Errors:" . PHP_EOL;
                print_r($errors);
                echo PHP_EOL;

                echo "Validations:" . PHP_EOL;
                print_r($validations);
                echo PHP_EOL;

                $this->fail('TFD signature failed.');
            }

            $this->assertEquals(true, $r);



            // Online validation
            $r = OnlineValidator::validate($cfdi);

            if ($r == 1) {
                $validations[] = [
                    'type' => 'online',
                    'success' => true,
                    'message' => 'CFDI is valid',
                ];
            } elseif ($r == 0) {
                $validations[] = [
                    'type' => 'online',
                    'success' => false,
                    'message' => 'CFDI is no longer valid',
                ];
            } else {
                // Error on the validation
                $validations[] = [
                    'type' => 'online',
                    'success' => false,
                    'message' => 'Online verification failed',
                ];
            }

            $this->assertEquals(1, $r);


            echo "Validations:" . PHP_EOL;
            print_r($validations);
            echo PHP_EOL;

        }

        unset($loader);
    }
}