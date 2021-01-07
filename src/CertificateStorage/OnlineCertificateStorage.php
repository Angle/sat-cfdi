<?php

namespace Angle\CFDI\CertificateStorage;

use Angle\CFDI\Utility\PathUtility;

use Angle\CFDI\Utility\OpenSSLUtility;

class OnlineCertificateStorage implements CertificateStorageInterface
{
    const BASE_URL = 'https://rdc.sat.gob.mx/rccf/';
    const TMP_DIRECTORY = '/tmp/sat-cfdi-certificates/';

    const DEFAULT_FALLBACK_DIRECTORY = '/../../resources/certificates/csd-sat/';

    /** @var string $fallbackDirectory */
    private $fallbackDirectory;

    /**
     * @var int $lastErrorType
     */
    private $lastErrorType = self::NO_ERROR;

    public function __construct(?string $directory = null)
    {
        if (!$directory) {
            $directory = realpath(__DIR__ . self::DEFAULT_FALLBACK_DIRECTORY);
        }

        $this->fallbackDirectory = $directory;

        // TODO: check if the file or directory exists..
    }

    public function getCertificatePEM($certificateNumber): ?string
    {
        $this->lastErrorType = self::NO_ERROR;

        // Clean the incoming string, only numbers allowed
        $certificateNumber = preg_replace('/[^0-9]+/', '', $certificateNumber);

        if (strlen($certificateNumber) != 20) {
            // Invalid length
            $this->lastErrorType = self::INVALID_CERTIFICATE_NUMBER;
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
                // TODO: Cannot read from our local filepath
                $this->lastErrorType = self::NOT_FOUND;
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
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // follow redirects
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        $response = curl_exec($ch);

        $chErrno = curl_errno($ch);
        $chHttpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        if ($chErrno != 0) { // previously: $chErrno == CURLE_OPERATION_TIMEDOUT
            // request failed for network reasons, attempt to load from a local file
            //error_log('SAT CFDI OnlineCertificateStorage query timed-out for: ' . $url);
            error_log('SAT CFDI OnlineCertificateStorage query error [curl ' . $chErrno . '] for: ' . $url);

            if ($this->fallbackDirectory === null) {
                // we don't have a fallback directory set, there's nothing else we can do
                $this->lastErrorType = self::NETWORK_ERROR;
                return null;
            }

            $filename = realpath(PathUtility::join($this->fallbackDirectory,  $certificateNumber . '.cer'));

            if (!file_exists($filename)) {
                // file was not found on our local storage, we'll set the error as a Network error
                $this->lastErrorType = self::NETWORK_ERROR;
                return null;
            }

            $response = file_get_contents($filename);

            if ($response === false) {
                // TODO: Cannot read from our local filepath
                $this->lastErrorType = self::NETWORK_ERROR;
                return null;
            }

        /*
        } elseif ($chHttpCode != 200) {
            // request failed for network reasons, attempt to load from a local file
            error_log('SAT CFDI OnlineCertificateStorage query error [http ' . $chHttpCode . '] for: ' . $url);

            if ($this->fallbackDirectory === null) {
                // we don't have a fallback directory set, there's nothing else we can do
                $this->lastErrorType = self::NETWORK_ERROR;
                return null;
            }

            $filename = realpath(PathUtility::join($this->fallbackDirectory,  $certificateNumber . '.cer'));

            if (!file_exists($filename)) {
                // file was not found on our local storage, we'll set the error as a Network error
                $this->lastErrorType = self::NETWORK_ERROR;
                return null;
            }

            $response = file_get_contents($filename);

            if ($response === false) {
                // TODO: Cannot read from our local filepath
                $this->lastErrorType = self::NETWORK_ERROR;
                return null;
            }
        */

        } elseif (!$response) {
            error_log('SAT CFDI OnlineCertificateStorage query failed [http ' . $chHttpCode . '] for: ' . $url);

            // the file was not found in SAT's LCO repository
            $this->lastErrorType = self::NOT_FOUND;
            return null;
        }


        // Request good, now we only have to convert it to the appropriate formats
        $this->lastErrorType = self::NO_ERROR;

        // All good, we'll have to convert it into a PEM
        if (strpos($response, '-----BEGIN CERTIFICATE-----') === 0) {
            // this certificate is already a PEM file, no need to do anything else
            $pem = $response;
        } else {
            // this certificate is binary, we have to coerce it
            $pem = OpenSSLUtility::coerceBinaryCertificate($response);
        }

        // now we'll write it into a temporary file
        // attempt to create the directory, disregard if successful or not
        @mkdir(PathUtility::join(self::TMP_DIRECTORY, $certificatePath), 0777, true);

        if (!is_dir(PathUtility::join(self::TMP_DIRECTORY, $certificatePath))) {
            // could not create the directory
            // we'll simply return the string as is, we'll figure out the writing part later on..
            return $pem;
        }

        if (file_put_contents($localPath, $pem) === false) {
            // file write failed
            // we'll simply return the string as is, we'll figure out the writing part later on..
            return $pem;
        }

        // all good! we saved a local copy in our temporary directory, we can now pass down the actual certificate
        return $pem;
    }

    public function getLastErrorType(): int
    {
        return $this->lastErrorType;
    }
}