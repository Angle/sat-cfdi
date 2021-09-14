<?php

namespace Angle\CFDI\Utility;

use FG\ASN1\ASNObject;
use FG\ASN1\Identifier;
use FG\ASN1\Universal\BitString;
use FG\ASN1\Universal\Sequence;

use DateTime;

abstract class X509VerificationUtility
{
    private const TRUSTED_ROOT_CERTIFICATE_DIRECTORY = '/../../resources/certificates/trusted-root-certificates/';

    private static $caCertificates = [];

    /**
     * Verify a X.509 Certificate against our local Trusted CA Certificates
     *
     * Returns 1 if the signature is correct, 0 if it is incorrect, and -1 on error.
     *
     * @param string $certificatePem PEM encoded certificate (base64 chunked)
     * @return int
     */
    public static function verifySignature(string $certificatePem): int
    {
        /* In case we receive a DER file and have to convert it to PEM
        if (strpos($certificateDer, '-----BEGIN CERTIFICATE-----') === 0) {
            // this certificate is already a PEM file, no need to do anything else
            $pem = $certificateDer;
        } else {
            // this certificate is binary, we have to coerce it
            $pem = OpenSSLUtility::coerceBinaryCertificate($certificateDer);
        }
        */

        $certificateDer = self::certificatePemToDer($certificatePem);

        if (!$certificateDer) {
            // PEM reading failure
            return -1;
        }

        $certificate = @openssl_x509_read($certificatePem);

        if ($certificate === false) {
            // X509 Read failure
            return -1;
        }

        $certificateParsed = openssl_x509_parse($certificate);

        if (!$certificateParsed) {
            // X509 Parse failure
            return -1;
        }


        try {
            /** @var Sequence $certificateAsn */
            $certificateAsn = ASNObject::fromBinary($certificateDer);
        } catch (\Exception $e) {
            // Certificate ASN1 Parse failure
            return -1;
        }

        if ($certificateAsn->getNumberOfChildren() != 3) {
            // Certificate ASN1 invalid number of children, expecting 3
            return -1;
        }

        /** @var Sequence $certificateBodyAsn */
        $certificateBodyAsn = $certificateAsn->getChildren()[0];
        $certificateBodyBinary = $certificateBodyAsn->getBinary();

        /** @var BitString $certificateSignatureAsn */
        $certificateSignatureAsn = $certificateAsn->getChildren()[2];
        // The BitString implementation has 3 methods that are more or less the same:
        // - binary(): returns the complete node binary, including the "headers"
        // - binaryContent(): returns the content binary, but prepends a byte of "unused bytes", in this case, it's always 0
        // - content(): returns a _human-readable_ (hex) representation of the value.
        // The simplest method to extract the actual signature binary is to apply a hex2bin() to the content() returns
        $certificateSignatureBinary = hex2bin($certificateSignatureAsn->getContent());

        $signatureType = $certificateParsed['signatureTypeLN'];

        if ($signatureType !== 'sha256WithRSAEncryption') {
            // Invalid SignatureType, expecting 'sha256WithRSAEncryption'
            return -1;
        }

        // try each and every trusted CA key..
        self::loadCACertificates();

        $decrypted = false;
        $hashInSignature = null;
        foreach (self::$caCertificates as $c) {
            // attempt to decrypt the signature that was found in the body against the public keys of the known Local Trusted CAs
            $r = openssl_public_decrypt($certificateSignatureBinary, $decryptedSignatureBinary, $c['public_key']);

            if ($r) {
                // Decrypt success! attempt to parse the result

                try {
                    /** @var Sequence $signatureAsn */
                    $signatureAsn = ASNObject::fromBinary($decryptedSignatureBinary);
                } catch (\Exception $e) {
                    // Signature ASN1 Parse failure
                    return -1;
                }

                if ($signatureAsn->getNumberOfChildren() != 2) {
                    // Signature ASN1 invalid number of children, expecting 2
                    return -1;
                }

                // Extract the Certificate Hash that was encrypted in the signature
                // See the explanation for $certificateSignatureBinary above
                $hashInSignature = hex2bin($signatureAsn->getChildren()[1]->getContent());
                $decrypted = true;

                break;
            }
        }

        if (!$decrypted) {
            // unable to decrypt the signature, this means none of the Local Trusted CA signed this certificate
            return 0;
        }

        // Compare the Certificate Body Hash to the Hash that was encrypted in the signature
        $certificateBodyHash = openssl_digest($certificateBodyBinary, 'SHA256', true);

        if ($certificateBodyHash == $hashInSignature) {
            return 1;
        } else {
            // hash mismatched
            return 0;
        }
    }

    /**
     * Verify a X.509 Certificate at a given date
     *
     * Returns 1 if dates are correct, 0 if it is incorrect, and -1 on error.
     *
     * @param string $certificatePem PEM encoded certificate (base64 chunked)
     * @param DateTime $date evaluation date
     * @return int
     */
    public static function verifyCertificateAtDate(string $certificatePem, DateTime $date)
    {
        $certificate = @openssl_x509_read($certificatePem);

        if ($certificate === false) {
            // X509 Read failure
            return -1;
        }

        $certificateParsed = openssl_x509_parse($certificate);

        if (!$certificateParsed) {
            // X509 Parse failure
            return -1;
        }

        try {
            // Calculate the ValidFrom Date
            $validFrom = new DateTime();
            $validFrom->setTimestamp($certificateParsed['validFrom_time_t']);

            // Calculate the ValidTo Date
            $validTo = new DateTime();
            $validTo->setTimestamp($certificateParsed['validTo_time_t']);
        } catch (\Exception $e) {
            // something failed when initializing the dates
            return -1;
        }

        if ($date < $validFrom) {
            // the date is _before_ the Certificate was created
            return 0;
        }

        if ($date > $validTo) {
            // the date is _after_ the Certificate expired
            return 0;
        }

        return 1;
    }


    /**
     * Load the local Trusted CA Certificates into memory
     */
    private static function loadCACertificates(): void
    {
        if (!empty(self::$caCertificates)) {
            // the certificates have already been loaded
            return;
        }

        $caCertificates = [];

        // Load Trusted CA's, first the .cer which are DER certificates (binary)
        $files = glob(__DIR__ . self::TRUSTED_ROOT_CERTIFICATE_DIRECTORY . '*.cer', GLOB_ERR);

        foreach ($files as $filename) {
            // The certificate is DER, we have to convert it to PEM
            $rootCertificateDer = file_get_contents(realpath($filename));
            $rootCertificatePem = OpenSSLUtility::coerceBinaryCertificate($rootCertificateDer);
            $rootCertificate = @openssl_x509_read($rootCertificatePem);

            $rootCertificateParsed = openssl_x509_parse($rootCertificate);

            $rootPublicKey = openssl_pkey_get_public($rootCertificate);
            $rootPublicKeyDetails = openssl_pkey_get_details($rootPublicKey);

            $caCertificates[$rootCertificateParsed['hash']] = [
                'filename'      => realpath($filename),
                'certificate'   => $rootCertificate,
                'parsed'        => $rootCertificateParsed,
                'pem'           => $rootCertificatePem,
                'der'           => $rootCertificateDer,
                'public_key'    => $rootPublicKeyDetails['key'],
                'hash'          => $rootCertificateParsed['hash'],
            ];
        }

        // Load Trusted CA's, then the .crt which are PEM certificates (base64)
        $files = glob(__DIR__ . self::TRUSTED_ROOT_CERTIFICATE_DIRECTORY . '*.crt', GLOB_ERR);

        foreach ($files as $filename) {
            // The certificate is PEM, we can use it as-is, but we'll still need the DER file later on
            $rootCertificatePem = file_get_contents(realpath($filename));
            $rootCertificateDer = self::certificatePemToDer($rootCertificatePem);
            $rootCertificate = @openssl_x509_read($rootCertificatePem);

            $rootCertificateParsed = openssl_x509_parse($rootCertificate);

            $rootPublicKey = openssl_pkey_get_public($rootCertificate);
            $rootPublicKeyDetails = openssl_pkey_get_details($rootPublicKey);

            $caCertificates[$rootCertificateParsed['hash']] = [
                'filename'      => realpath($filename),
                'certificate'   => $rootCertificate,
                'parsed'        => $rootCertificateParsed,
                'pem'           => $rootCertificatePem,
                'der'           => $rootCertificateDer,
                'public_key'    => $rootPublicKeyDetails['key'],
                'hash'          => $rootCertificateParsed['hash'],
            ];
        }

        // Store it in our Static variable
        self::$caCertificates = $caCertificates;

        return;
    }

    /**
     * @param string $pem source PEM string
     * @return string converted DER
     */
    private static function certificatePemToDer(string $pem): ?string
    {
        $begin  = "-----BEGIN CERTIFICATE-----\n";
        $end    = "-----END CERTIFICATE-----\n";

        if (strpos($pem, $begin) === false) {
            // the certificate is not a valid PEM file
            return null;
        }

        $data = substr($pem, strlen($begin), -1*strlen($end));
        $data = str_replace('\n', '', $data);

        return base64_decode($data);
    }

    private static function printASNObject(ASNObject $object, $depth = 0)
    {
        $treeSymbol = '';
        $depthString = str_repeat('─', $depth);
        if ($depth > 0) {
            $treeSymbol = '├';
        }

        $name = Identifier::getShortName($object->getType());
        echo "{$treeSymbol}{$depthString}{$name} : ";

        echo $object->__toString().PHP_EOL;

        $content = $object->getContent();
        if (is_array($content)) {
            foreach ($object as $child) {
                self::printASNObject($child, $depth + 1);
            }
        }
    }
}