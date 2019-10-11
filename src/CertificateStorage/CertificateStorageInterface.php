<?php

namespace Angle\CFDI\CertificateStorage;

interface CertificateStorageInterface
{
    /**
     * Find and get a Certificate for a given certificate number. The result must be a valid PEM-formatted string
     * @param $certificateNumber
     * @return string|null PEM-formatted certificate data, null if not found
     */
    public function getCertificatePEM($certificateNumber): ?string;
}