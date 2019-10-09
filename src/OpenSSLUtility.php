<?php

namespace Angle\CFDI;

abstract class OpenSSLUtility
{
    const TRUSTED_CA_PEM = __DIR__ . '/../resources/certificates/sat-trusted-ca-prod.pem';
    const SAT_STAMP_CERTIFICATE_DIR = __DIR__ . '/../resources/certificates/csd-sat';

    /**
     * Coerce a base64-encoded certificate into a valid X.509 certificate to be read by OpenSSL
     * @param $certificateInBase64
     * @return string
     */
    public static function coerceBase64Certificate($certificateInBase64) {
        $bin = base64_decode($certificateInBase64, true);

        $pem = chunk_split(base64_encode($bin), 64, "\n");
        $pem = "-----BEGIN CERTIFICATE-----\n".$pem."-----END CERTIFICATE-----\n";
        return $pem;
    }

    /**
     * Coerce a base64-encoded certificate into a valid X.509 certificate to be read by OpenSSL
     * @param $certificateBinary
     * @return string
     */
    public static function coerceBinaryCertificate($certificateBinary) {
        $pem = chunk_split(base64_encode($certificateBinary), 64, "\n");
        $pem = "-----BEGIN CERTIFICATE-----\n".$pem."-----END CERTIFICATE-----\n";
        return $pem;
    }

    /**
     * Implode any OpenSSL errors into a single string
     * @return string
     */
    public static function getOpenSSLErrorsAsString(): string
    {
        $errors = [];
        while ($msg = openssl_error_string()) {
            $errors[] = $msg;
        }

        if (empty($errors)) {
            return '';
        } else {
            return implode(' // ', $errors);
        }
    }

    /**
     * @param $certificateNumber
     * @return string|null
     */
    public static function findSatStampCertificateByNumber($certificateNumber): ?string
    {
        // Clean the incoming string, only numbers allowed
        $certificateNumber = preg_replace('/[^0-9]+/', '', $certificateNumber);

        $filename = realpath(self::SAT_STAMP_CERTIFICATE_DIR . '/' . $certificateNumber . '.cer');

        if (file_exists($filename)) {
            return $filename;
        } else {
            return null;
        }
    }




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
                $pem = self::coerceBinaryCertificate($certData);
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
                $pem = self::coerceBinaryCertificate($certData);
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