<?php

namespace Angle\CFDI\Invoice\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\Invoice\CFDINode;

use Angle\CFDI\OpenSSLUtility;
use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * @method static FiscalStamp createFromDOMNode(DOMNode $node)
 */
class FiscalStamp extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION = "1.1";

    const NODE_NAME = "TimbreFiscalDigital";
    const NS_NODE_NAME = "tfd:TimbreFiscalDigital";

    protected static $baseAttributes = [
        'xmlns:tfd' => "http://www.sat.gob.mx/TimbreFiscalDigital",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/TimbreFiscalDigital http://www.sat.gob.mx/sitio_internet/cfd/TimbreFiscalDigital/TimbreFiscalDigitalv11.xsd",
    ];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'version'           => [
            'keywords' => ['Version', 'version'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'uuid'          => [
            'keywords' => ['UUID', 'uuid'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'stampDate'        => [
            'keywords' => ['FechaTimbrado', 'stampDate'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'certificateProviderRfc'        => [
            'keywords' => ['RfcProvCertif', 'certificateProviderRfc'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'legend'        => [
            'keywords' => ['Leyenda', 'legend'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'cfdiSignature'        => [
            'keywords' => ['SelloCFD', 'cfdiSignature'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'satCertificateNumber'        => [
            'keywords' => ['NoCertificadoSAT', 'satCertificateNumber'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'satSignature'        => [
            'keywords' => ['SelloSAT', 'satSignature'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];



    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $version = self::VERSION;

    /**
     * @var string
     */
    protected $uuid;

    /**
     * @var DateTime
     */
    protected $stampDate;

    /**
     * @var string
     */
    protected $certificateProviderRfc;

    /**
     * @var string
     */
    protected $legend;

    /**
     * @var string
     */
    protected $cfdiSignature;

    /**
     * @var string
     */
    protected $satCertificateNumber;

    /**
     * @var string
     */
    protected $satSignature;
    

    #########################
    ##     CONSTRUCTOR     ##
    #########################

    // constructor implemented in the CFDINode abstract class

    /**
     * @param DOMNode[]
     * @throws CFDIException
     */
    public function setChildren(array $children): void
    {
        // void
    }


    #########################
    ## INVOICE TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElement(self::NS_NODE_NAME);

        foreach ($this->getAttributes() as $attr => $value) {
            $node->setAttribute($attr, $value);
        }

        // no child nodes

        return $node;
    }


    #########################
    ## VALIDATION
    #########################

    public function validate(): bool
    {
        // TODO: implement the full set of validation, including type and Business Logic

        return true;
    }

    public function getChainSequence(): string
    {
        $items = [];
        // Build the Original Chain Sequence for the Fiscal Stamp
        $c = "||";

        $items[] = $this->version;
        $items[] = $this->uuid;

        if (!($this->stampDate instanceof DateTime)) {
            throw new CFDIException('StampDate is not a valid DateTime');
        }

        $items[] = $this->stampDate->format(CFDI::DATETIME_FORMAT);
        $items[] = $this->certificateProviderRfc;

        if ($this->legend) {
            $items[] = $this->legend;
        }

        $items[] = $this->cfdiSignature;
        $items[] = $this->satCertificateNumber;

        $items = array_map('Angle\CFDI\CFDI::cleanWhitespace', $items);

        return '||' . implode('|', $items) . '||';
    }

    /**
     * On success, returns 0
     * On failure, returns an array with any validation errors encountered.
     * @return array|0
     */
    public function checkSignature()
    {
        // LOOK UP THE CERTIFICATE NUMBER
        if (!$this->satCertificateNumber) {
            return ['TFD does not have a SAT Certificate Number'];
        }

        $certificateFile = OpenSSLUtility::findSatStampCertificateByNumber($this->satCertificateNumber);

        if (!$certificateFile) {
            // TODO: What should we do here? should we auto-download all the certificates?
            return ['Certificate file not found for "' . $this->satCertificateNumber . '"'];
        }

        $certificateData = file_get_contents($certificateFile);

        if ($certificateData === false) {
            return ['Certificate file for "' . $this->satCertificateNumber . '" cannot be read'];
        }

        $certificatePem = OpenSSLUtility::coerceBinaryCertificate($certificateData);

        $certificate = openssl_x509_read($certificatePem);

        if ($certificate === false) {
            return ['Certificate X.509 read failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        }

        // TODO: we could skip this step, we don't actually have to check if the Certificate is valid since it's in our local trusted store
        // Check the certificate's CA
        $auth = openssl_x509_checkpurpose($certificate, X509_PURPOSE_ANY, [OpenSSLUtility::TRUSTED_CA_PEM]);
        if ($auth === false) {
            return ['Certificate not authentic: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        } elseif ($auth === -1) {
            return ['Certificate authenticity check failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        }



        ////////////////
        /// VALIDATE THE SIGNATURE

        if (!$this->satSignature) {
            return ['TFD does not have a SAT Signature'];
        }

        $signature = base64_decode($this->satSignature, true);

        if ($signature === false) {
            return ['Cannot decode SAT signature'];
        }

        // Build the Original Chain Sequence
        // TODO: Check if the "original chain sequence" is properly built and compare it against the signature
        $chain = $this->getChainSequence();

        echo "TFD Chain: " . $chain . PHP_EOL;

        $publicKey = openssl_pkey_get_public($certificate);

        if ($publicKey === false) {
            return ['Public Key extraction failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        }

        // Verify the given signature with the Chain
        // Returns 1 if the signature is correct, 0 if it is incorrect, and -1 on error.
        $r = openssl_verify($chain, $signature, $publicKey, OPENSSL_ALGO_SHA256);

        if ($r === 0) {
            return ['Signature is incorrect: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        } elseif ($r === -1) {
            return ['Signature verification failed: ' . OpenSSLUtility::getOpenSSLErrorsAsString()];
        }

        return 0;
    }


    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return FiscalStamp
     */
    public function setVersion(?string $version): self
    {
        // Note: this value is fixed, it cannot be set or changed
        //$this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     * @return FiscalStamp
     */
    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getStampDate(): ?DateTime
    {
        return $this->stampDate;
    }

    /**
     * @param DateTime|string $rawDate
     * @throws CFDIException
     * @return FiscalStamp
     */
    public function setStampDate($rawDate): self
    {
        if ($rawDate instanceof DateTime) {
            $this->stampDate = $rawDate;
        }

        // sample format: 2019-09-06T10:09:46
        // TODO: We are assuming that dates ARE in Mexico City's timezone
        try {
            $tz = new DateTimeZone(CFDI::DATETIME_TIMEZONE);
            $date = DateTime::createFromFormat(CFDI::DATETIME_FORMAT, $rawDate, $tz);
        } catch (\Exception $e) {
            throw new CFDIException('Raw date string is in invalid format, cannot parse stamp date');
        }

        $this->stampDate = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getCertificateProviderRfc(): ?string
    {
        return $this->certificateProviderRfc;
    }

    /**
     * @param string $certificateProviderRfc
     * @return FiscalStamp
     */
    public function setCertificateProviderRfc(?string $certificateProviderRfc): self
    {
        $this->certificateProviderRfc = $certificateProviderRfc;
        return $this;
    }

    /**
     * @return string
     */
    public function getLegend(): ?string
    {
        return $this->legend;
    }

    /**
     * @param string $legend
     * @return FiscalStamp
     */
    public function setLegend(?string $legend): self
    {
        $this->legend = $legend;
        return $this;
    }

    /**
     * @return string
     */
    public function getCfdiSignature(): ?string
    {
        return $this->cfdiSignature;
    }

    /**
     * @param string $cfdiSignature
     * @return FiscalStamp
     */
    public function setCfdiSignature(?string $cfdiSignature): self
    {
        $this->cfdiSignature = $cfdiSignature;
        return $this;
    }

    /**
     * @return string
     */
    public function getSatCertificateNumber(): ?string
    {
        return $this->satCertificateNumber;
    }

    /**
     * @param string $satCertificateNumber
     * @return FiscalStamp
     */
    public function setSatCertificateNumber(?string $satCertificateNumber): self
    {
        $this->satCertificateNumber = $satCertificateNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getSatSignature(): ?string
    {
        return $this->satSignature;
    }

    /**
     * @param string $satSignature
     * @return FiscalStamp
     */
    public function setSatSignature(?string $satSignature): self
    {
        $this->satSignature = $satSignature;
        return $this;
    }
}