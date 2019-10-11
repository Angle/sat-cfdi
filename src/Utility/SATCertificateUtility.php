<?php

namespace Angle\CFDI\Utility;

use Angle\CFDI\Utility\OpenSSLUtility;

/**
 * HELPER FUNCTIONS FOR LOCAL DEBUG. SHOULD NOT BE USED IN PRODUCTION CODE!
 * Class SATCertificateUtility
 * @package Angle\CFDI\Utility
 */
abstract class SATCertificateUtility
{
    ## HELPER FUNCTIONS, SHOULD NOT BE USED IN CODE

    /**
     * Prints to stdout the merged certificates as a PEM chain
     */
    public static function mergeCertificates()
    {
        $merged = "";

        foreach (glob(__DIR__ . '/../resources/certificates/original/*.{cer,crt}', GLOB_ERR|GLOB_BRACE) as $filename) {
            $filename = realpath($filename);

            $certData = file_get_contents($filename);

            if (strpos($certData, '-----BEGIN CERTIFICATE-----') === 0) {
                // this certificate is already a PEM file, no need to do anything else
                $pem = $certData;
            } else {
                // this certificate is binary, we have to coerce it
                $pem = OpenSSLUtility::coerceBinaryCertificate($certData);
            }

            $merged .= $pem;
        }

        echo PHP_EOL . PHP_EOL;
        echo "~~~~ MERGED CERTIFICATES ~~~~";
        echo PHP_EOL;
        echo $merged;
        echo PHP_EOL . PHP_EOL;
    }

    /**
     * Prints to stdout the merged certificates as a PEM chain
     */
    public static function findCertificateSerial()
    {
        foreach (glob(__DIR__ . '/../resources/certificates/trusted-root-certificates/*.{cer,crt}', GLOB_ERR|GLOB_BRACE) as $filename) {
            $filename = realpath($filename);

            $certData = file_get_contents($filename);

            if (strpos($certData, '-----BEGIN CERTIFICATE-----') === 0) {
                // this certificate is already a PEM file, no need to do anything else
                $pem = $certData;
            } else {
                // this certificate is binary, we have to coerce it
                $pem = OpenSSLUtility::coerceBinaryCertificate($certData);
            }

            $certificate = openssl_x509_read($pem);

            if ($certificate === false) {
                throw new \Exception('Certificate X.509 read failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString());
            }

            $parsedCertificate = openssl_x509_parse($certificate);

            //print_r($parsedCertificate);

            echo $parsedCertificate['serialNumber'] . ": " . $filename . PHP_EOL;
        }
    }

}