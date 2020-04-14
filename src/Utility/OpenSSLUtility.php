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
     * Coerce a base64-encoded Private Key into a valid format to be read by OpenSSL
     * @param $privateKeyBinary
     * @return string
     */
    public static function coerceBinaryPrivateKey($privateKeyBinary) {
        $pem = chunk_split(base64_encode($privateKeyBinary), 64, "\n");
        $pem = "-----BEGIN RSA PRIVATE KEY-----\n".$pem."-----END RSA PRIVATE KEY-----\n";
        return $pem;
    }

    /**
     * Coerce a base64-encoded Private Key into a valid format to be read by OpenSSL
     * @param $privateKeyBinary
     * @return string
     */
    public static function coerceBinaryEncryptedPrivateKey($privateKeyBinary) {
        $pem = chunk_split(base64_encode($privateKeyBinary), 64, "\n");
        $pem = "-----BEGIN ENCRYPTED PRIVATE KEY-----\n".$pem."-----END ENCRYPTED PRIVATE KEY-----\n";
        return $pem;
    }

    /**
     * Get OpenSSL errors as an array
     * @return array
     */
    public static function getOpenSSLErrors(): array
    {
        $errors = [];
        while ($msg = openssl_error_string()) {
            $errors[] = $msg;
        }

        return $errors;
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