<?php

namespace Angle\CFDI\Tests;

use Angle\CFDI\Utility\OpenSSLUtility;
use Angle\CFDI\Utility\X509VerificationUtility;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;
use PHPUnit\Framework\TestCase;

use Angle\CFDI\CFDIInterface;
use Angle\CFDI\XmlLoader;

use FG\ASN1\Identifier;
use FG\ASN1\ASNObject;


final class X509VerificationTest extends TestCase
{
    public function testValidate(): void
    {
        $files = glob(__DIR__ . '/../resources/certificates/csd-sat/*.cer', GLOB_ERR);

        $realFiles = [];
        foreach ($files as $filename) {
            $realFiles[] = realpath($filename);
        }

        echo PHP_EOL;

        foreach ($realFiles as $filename) {
            echo "- Certificate: " . basename($filename) . '.. ';

            $certificateDer = file_get_contents($filename);

            if (strpos($certificateDer, '-----BEGIN CERTIFICATE-----') === 0) {
                // this certificate is already a PEM file, no need to do anything else
                $certificatePem = $certificateDer;
            } else {
                // this certificate is binary, we have to coerce it
                $certificatePem = OpenSSLUtility::coerceBinaryCertificate($certificateDer);
            }

            $r = X509VerificationUtility::verifySignature($certificatePem);

            $this->assertEquals(1, $r);

            if ($r == 1) {
                echo "valid!" . PHP_EOL;
            } elseif ($r == 0) {
                echo "not authentic" . PHP_EOL;
            } else {
                echo "verification failed" . PHP_EOL;
            }
        }
    }
}