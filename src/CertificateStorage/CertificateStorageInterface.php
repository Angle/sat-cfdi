<?php

namespace Angle\CFDI\CertificateStorage;

interface CertificateStorageInterface
{
    const NO_ERROR = 0;
    const NOT_FOUND = 1;
    const NETWORK_ERROR = 2;
    const INVALID_CERTIFICATE_NUMBER = 3;

    /**
     * Find and get a Certificate for a given certificate number. The result must be a valid PEM-formatted string
     * @param $certificateNumber
     * @return string|null PEM-formatted certificate data, null if not found
     */
    public function getCertificatePEM($certificateNumber): ?string;

    public function getLastErrorType(): int;
}