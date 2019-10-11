<?php

namespace Angle\CFDI\Utility;

abstract class OpenSSLUtility
{

    /**
     * Coerce a base64-encoded certificate into a valid X.509 certificate to be read by OpenSSL
     * @param $certificateInBase64
     * @return string
     */
    public static function coerceBase64Certificate($certificateInBase64) {
        $bin = base64_decode($certificateInBase64, true);

        if ($bin === false) {
            return "";
        }

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
}