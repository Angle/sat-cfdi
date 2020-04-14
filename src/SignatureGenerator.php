<?php

namespace Angle\CFDI;

use Angle\CFDI\Utility\OpenSSLUtility;

class SignatureGenerator
{
    /**
     * Formatted libxml Error details
     * @var array
     */
    private $errors = [];

    /**
     * @param CFDI $cfdi
     * @param string $privateKey raw binary private key
     * @param string $passphrase plaintext string
     * @return string|false returns a base64 encoded string with the signature, false on failure
     */
    public function generateCfdiSignature(CFDI $cfdi, $privateKey, $passphrase)
    {
        // Build the Original Chain
        $chainProcessor = new OriginalChainGenerator();
        $chain = $chainProcessor->generateForCFDI($cfdi);

        if ($chain === false) {
            $this->errors = array_merge($this->errors, $chainProcessor->getErrors());
            return false;
        }

        // Free resources and clear streams
        unset($chainProcessor);

        $privateKeyPem = OpenSSLUtility::coerceBinaryCertificate($privateKey);

        // Decrypt the PrivateKey using the passphrase
        $private = openssl_pkey_get_private($privateKeyPem, $passphrase);

        $r = openssl_sign($chain, $signature, $private, OPENSSL_ALGO_SHA256);

        if (!$r) {
            $this->errors = array_merge($this->errors, OpenSSLUtility::getOpenSSLErrors());
            return false;
        }

        return base64_encode($signature);
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }
}