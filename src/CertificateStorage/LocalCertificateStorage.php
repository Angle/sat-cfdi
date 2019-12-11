<?php

namespace Angle\CFDI\CertificateStorage;

use Angle\CFDI\Utility\PathUtility;

use Angle\CFDI\Utility\OpenSSLUtility;

class LocalCertificateStorage implements CertificateStorageInterface
{
    /** @var string $directory */
    private $directory;

    /**
     * @var int $lastErrorType
     */
    private $lastErrorType = self::NO_ERROR;

    public function __construct(string $directory)
    {
        $this->directory = $directory;

        // TODO: check if the file or directory exists..
    }

    public function getCertificatePEM($certificateNumber): ?string
    {
        $this->lastErrorType = self::NOT_FOUND;
        // Clean the incoming string, only numbers allowed
        $certificateNumber = preg_replace('/[^0-9]+/', '', $certificateNumber);

        $filename = realpath(PathUtility::join($this->directory,  $certificateNumber . '.cer'));

        if (!file_exists($filename)) {
            // TODO: error not found
            $this->lastErrorType = self::NOT_FOUND;
            return null;
        }

        // TODO: error cant read
        $certificateData = file_get_contents($filename);

        if (strpos($certificateData, '-----BEGIN CERTIFICATE-----') === 0) {
            // this certificate is already a PEM file, no need to do anything else
            $pem = $certificateData;
        } else {
            // this certificate is binary, we have to coerce it
            $pem = OpenSSLUtility::coerceBinaryCertificate($certificateData);
        }

        $this->lastErrorType = self::NO_ERROR;

        return $pem;
    }

    public function getLastErrorType(): int
    {
        return $this->lastErrorType;
    }

}