<?php

namespace Angle\CFDI;

use Angle\CFDI\CertificateStorage\CertificateStorageInterface;

use Angle\CFDI\Utility\OpenSSLUtility;

use Angle\CFDI\CFDI;
use Angle\CFDI\Node\FiscalStamp;

class SignatureValidator
{
    /** @var CertificateStorageInterface */
    private $certificateStorage;

    /**
     * Validations array, in the format: [{type: string, success: true/false, message: string}]
     * @var array
     */
    private $validations = [];

    /**
     * @param CertificateStorageInterface $certificateStorage used to query for certificates
     */
    public function __construct(CertificateStorageInterface $certificateStorage)
    {
        $this->certificateStorage = $certificateStorage;
    }

    /**
     * @return bool
     */
    public function checkCfdiSignature(CFDI $cfdi)
    {
        // Reset any previous validations
        $this->validations = [];

        if ($cfdi->getVersion() != CFDI::VERSION_3_3) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'CFDI Signature check is only implemented for CFDI v3.3',
            ];
            return false;
        }

        $this->validations[] = [
            'type' => 'signature:cfdi',
            'success' => true,
            'message' => 'CFDI version is 3.3',
        ];


        /////////
        // VALIDATE THE CERTIFICATE

        if (!$cfdi->getCertificate()) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'CFDI does not have an Issuer Certificate',
            ];
            return false;
        }

        $certificatePem = OpenSSLUtility::coerceBase64Certificate($cfdi->getCertificate());

        $certificate = openssl_x509_read($certificatePem);

        if ($certificate === false) {
            //return ['Certificate X.509 read failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'Issuer certificate data in CFDI cannot be read as a X.509 Certificate',
            ];
            return false;
        }

        $parsedCertificate = openssl_x509_parse($certificate);

        // Check that the Certificate matches the CFDI Issuer's RFC
        if (!array_key_exists('subject', $parsedCertificate)
            || !array_key_exists('x500UniqueIdentifier', $parsedCertificate['subject'])
            || !$parsedCertificate['subject']['x500UniqueIdentifier']) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'Issuer X.509 Certificate does not have a valid Subject x500UniqueIdentifier',
            ];
            return false;
        }

        // Extract and clean up the RFC
        $issuerRfc = explode('/', $parsedCertificate['subject']['x500UniqueIdentifier']);
        $issuerRfc = trim($issuerRfc[0]);

        if (!$cfdi->getIssuer()) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'CFDI does not have an Issuer',
            ];
            return false;
        }

        if ($cfdi->getIssuer()->getRfc() != $issuerRfc) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'CFDI Issuer\'s RFC does not match the X.509 Certificate\'s x500UniqueIdentifier',
            ];
            return false;
        }


        // LCO COMPARE
        // Load the Issuer's Certificate from SAT LCO (Lista de Contribuyentes Obligados) and make sure that it matches the on in the CFDI
        $lcoCertificatePem = $this->certificateStorage->getCertificatePEM($cfdi->getCertificateNumber());

        if (!$lcoCertificatePem) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'Issuer Certificate was not found in SAT LCO repository',
            ];
            return false;
        }

        $lcoCertificate = openssl_x509_read($lcoCertificatePem);

        if ($lcoCertificate === false) {
            //return ['Certificate X.509 read failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'Issuer Certificate data from SAT LCO cannot be read as a X.509 Certificate',
            ];
            return false;
        }

        $cerFingerprint = (string)openssl_x509_fingerprint($certificate, 'sha256');
        $lcoFingerprint = (String)openssl_x509_fingerprint($lcoCertificate, 'sha256');

        if ($cerFingerprint === false || $lcoFingerprint === false) {
            //return ['Certificate X.509 fingerprint failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'X.509 Certificate fingerprint generation failed',
            ];
            return false;
        }

        if ($cerFingerprint !== $lcoFingerprint) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'Issuer CFDI X.509 Certificate does not match SAT LCO',
            ];
            return false;
        }

        $this->validations[] = [
            'type' => 'signature:cfdi',
            'success' => true,
            'message' => 'Issuer X.509 Certificate found on SAT LCO',
        ];


        // Check the certificate's CA
        $auth = openssl_x509_checkpurpose($certificate, X509_PURPOSE_ANY, [CFDI::SATRootCertificatePEM()]);
        if ($auth === false) {
            //return ['Certificate not authentic: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'Issuer X.509 Certificate is not authentic',
            ];
            return false;
        } elseif ($auth === -1) {
            //return ['Certificate authenticity check failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'Issuer X.509 Certificate authenticity check failed',
            ];
            return false;
        }

        $this->validations[] = [
            'type' => 'signature:cfdi',
            'success' => true,
            'message' => 'Issuer X.509 Certificate was issued by SAT',
        ];



        ////////////////
        /// VALIDATE THE SIGNATURE

        if (!$cfdi->getSignature()) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'CFDI does not have a signature',
            ];
            return false;
        }

        $signature = base64_decode($cfdi->getSignature(), true);

        if ($signature === false) {
            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'CFDI signature cannot be decoded as base64',
            ];
            return false;
        }

        // Build the Original Chain Sequence
        // TODO: Check if the "original chain sequence" is properly built and compare it against the signature
        $chain = $cfdi->getChainSequence();

        $publicKey = openssl_pkey_get_public($certificate);

        if ($publicKey === false) {
            //return ['Public Key extraction failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'Issuer X.509 Certificate public key extraction failed',
            ];
            return false;
        }

        // Verify the given signature with the Chain
        // Returns 1 if the signature is correct, 0 if it is incorrect, and -1 on error.
        $r = openssl_verify($chain, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        if ($r === 0) {
            //return ['Signature is incorrect: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'CFDI Signature is not valid',
            ];
            return false;
        } elseif ($r === -1) {
            //return ['Signature verification failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:cfdi',
                'success' => false,
                'message' => 'CFDI Signature authenticity verification failed',
            ];
            return false;
        }

        $this->validations[] = [
            'type' => 'signature:cfdi',
            'success' => true,
            'message' => 'CFDI Signature is valid',
        ];

        return true;

    }

    /**
     *
     * TODO: Change this, change the validation response
     *
     * On success, returns 0
     * On failure, returns an array with any validation errors encountered.
     * @param CFDI $cfdi
     * @return array|int
     */
    public function checkFiscalStampSignature(CFDI $cfdi)
    {
        // Reset any previous validations
        $this->validations = [];

        $fiscalStamp = $cfdi->getFiscalStamp();

        if (!$fiscalStamp || !($fiscalStamp instanceof FiscalStamp)) {
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'CFDI does not have a TFD Complement',
            ];
            return false;
        }



        if ($fiscalStamp->getVersion() != FiscalStamp::VERSION_1_1) {
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'FiscalStamp Signature check is only implemented for TFD v1.1',
            ];
            return false;
        }

        $this->validations[] = [
            'type' => 'signature:tfd',
            'success' => true,
            'message' => 'TFD version is 1.1',
        ];

        // The CFDI signature should be exactly the same as the one in the FiscalStamp node
        if ($cfdi->getSignature() !== $fiscalStamp->getCfdiSignature()) {
            // CFDI Signature mismatched
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'CFDI Signature is not identical to TFD Signature',
            ];
            return false;
        }

        ////////////////
        /// VALIDATE THE CERTIFICATE

        // Look up the certificate number
        if (!$fiscalStamp->getSatCertificateNumber()) {
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'TFD does not have a SAT Certificate Number',
            ];
            return false;
        }


        $certificatePem = $this->certificateStorage->getCertificatePEM($fiscalStamp->getSatCertificateNumber());

        if (!$certificatePem) {
            // Not found (get error?)
            // TODO: Error, pull the errors from the certificate storage
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'SAT Certificate was not found',
            ];
            return false;
        }

        $certificate = openssl_x509_read($certificatePem);

        if ($certificate === false) {
            //return ['Certificate X.509 read failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'SAT X.509 Certificate cannot be read',
            ];
            return false;
        }

        $this->validations[] = [
            'type' => 'signature:tfd',
            'success' => true,
            'message' => 'SAT X.509 Certificate found on SAT LCO',
        ];


        // Check the certificate's CA
        $auth = openssl_x509_checkpurpose($certificate, X509_PURPOSE_ANY, [CFDI::SATRootCertificatePEM()]);
        if ($auth === false) {
            //return ['Certificate not authentic: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'SAT X.509 Certificate is not authentic',
            ];
            return false;
        } elseif ($auth === -1) {
            //return ['Certificate authenticity check failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'SAT X.509 Certificate authenticity check failed',
            ];
            return false;
        }

        $this->validations[] = [
            'type' => 'signature:tfd',
            'success' => true,
            'message' => 'SAT X.509 Certificate is valid',
        ];


        ////////////////
        /// VALIDATE THE SIGNATURE

        if (!$fiscalStamp->getSatSignature()) {
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'TFD does not have a SAT Signature',
            ];
            return false;
        }

        $signature = base64_decode($fiscalStamp->getSatSignature(), true);

        if ($signature === false) {
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'TFD Signature cannot be decoded as base64',
            ];
            return false;
        }


        // Build the Original Chain Sequence
        try {
            $chain = $fiscalStamp->getChainSequence();
        } catch (CFDIException $e) {
            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'TFD Chain Sequence cannot be generated',
            ];
            return false;
        }

        $publicKey = openssl_pkey_get_public($certificate);

        if ($publicKey === false) {
            //return ['Public Key extraction failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'SAT X.509 Certificate public key extraction failed',
            ];
            return false;
        }

        // Verify the given signature with the Chain
        // Returns 1 if the signature is correct, 0 if it is incorrect, and -1 on error.
        $r = openssl_verify($chain, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        if ($r === 0) {
            //return ['Signature is incorrect: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'TFD Signature is not valid',
            ];
            return false;
        } elseif ($r === -1) {
            //return ['Signature verification failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];

            $this->validations[] = [
                'type' => 'signature:tfd',
                'success' => false,
                'message' => 'TFD Signature authenticity verification failed',
            ];
            return false;
        }


        $this->validations[] = [
            'type' => 'signature:tfd',
            'success' => true,
            'message' => 'TFD Signature is valid',
        ];

        return true;
    }

    public function getValidations()
    {
        return $this->validations;
    }
}