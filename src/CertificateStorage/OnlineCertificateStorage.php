<?php

namespace Angle\CFDI\CertificateStorage;

use Angle\CFDI\Utility\PathUtility;

use Angle\CFDI\Utility\OpenSSLUtility;

class OnlineCertificateStorage implements CertificateStorageInterface
{
    const BASE_URL = 'https://rdc.sat.gob.mx/rccf/';
    const TMP_DIRECTORY = '/tmp/sat-cfdi-certificates/';

    public function getCertificatePEM($certificateNumber): ?string
    {
        // Clean the incoming string, only numbers allowed
        $certificateNumber = preg_replace('/[^0-9]+/', '', $certificateNumber);

        if (strlen($certificateNumber) != 20) {
            // Invalid length
            return null;
        }

        // Build the Certificate path from the CertificateNumber
        $certificatePath = substr($certificateNumber, 0, 6) . '/';
        $certificatePath .= substr($certificateNumber, 6, 6) . '/';
        $certificatePath .= substr($certificateNumber, 12, 2) . '/';
        $certificatePath .= substr($certificateNumber, 14, 2) . '/';
        $certificatePath .= substr($certificateNumber, 16, 2) . '/';

        $certificateFile = $certificateNumber . '.cer';

        // First check if we have it locally
        // TODO: how do we check if the file has been too long in tmp? Clear cache periodically?
        $localPath = PathUtility::join(self::TMP_DIRECTORY, $certificatePath, $certificateFile);

        if (file_exists($localPath)) {
            // we found it in our local storage, use this!
            $certificateData = file_get_contents($localPath);

            if ($certificateData === false) {
                return null;
            }

            return $certificateData;
        }

        // The file was not found in our local temporary storage, we'll have to download it from the internet

        $url = self::BASE_URL . $certificatePath . $certificateFile;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);

        $response = curl_exec($ch);

        curl_close($ch);

        if (!$response) {
            // request failed, there is nothing much we can do
            error_log('SAT CFDI OnlineCertificateStorage query failed for: ' . $url);
            return null;
        }

        // All good, we'll have to convert it into a PEM
        if (strpos($response, '-----BEGIN CERTIFICATE-----') === 0) {
            // this certificate is already a PEM file, no need to do anything else
            $pem = $response;
        } else {
            // this certificate is binary, we have to coerce it
            $pem = OpenSSLUtility::coerceBinaryCertificate($response);
        }

        // now we'll write it into a temporary file
        if (!mkdir(PathUtility::join(self::TMP_DIRECTORY, $certificatePath), 0777, true)) {
            // could not create the directory
            // we'll simply return the string as is, we'll figure out the writting part later on..
            return $pem;
        }

        if (file_put_contents($localPath, $pem) === false) {
            // file write failed
            // we'll simply return the string as is, we'll figure out the writting part later on..
            return $pem;
        }

        // all good! we saved a local copy in our temporary directory, we can now pass down the actual certificate
        return $pem;
    }

}