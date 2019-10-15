<?php

namespace Angle\CFDI;

use Angle\CFDI\Utility\PathUtility;

use XSLTProcessor;
use DOMDocument;
use LibXMLError;

use Genkgo\Xsl\Cache\ArrayCache;
use Genkgo\Xsl\ProcessorFactory;
use Genkgo\Xsl\XsltProcessor;

use Angle\CFDI\CFDI;
use Angle\CFDI\Node\FiscalStamp;

class OriginalChainGenerator
{
    // Relative to the project directory
    const XSLT_RESOURCES_DIR = '/resources/xslt-processor/';
    const XSD_WHITELIST = [
        'CFDI_3_3.xslt',
        'TFD_1_1.xslt',
    ];

    /**
     * Library directory
     * @var string
     */
    private $resourceDir;

    /**
     * Validations array, in the format: [{type: string, success: true/false, message: string}]
     * @var array
     */
    private $validations = [];


    public function __construct()
    {
        $libDir = PathUtility::join(__DIR__, '/../');

        $this->resourceDir = PathUtility::join($libDir, self::XSLT_RESOURCES_DIR);
    }

    /**
     * Generate the OriginalChain string for a given CFDI object
     * Returns false on failure
     * @param CFDI $cfdi
     * @return string|false
     */
    public function generateForCFDI(CFDI $cfdi)
    {
        libxml_use_internal_errors(true);
        libxml_clear_errors(); // clean up any previous errors found in other validations



        // Check that the version is correct
        if ($cfdi->getVersion() != CFDI::VERSION_3_3) {
            $this->validations[] = [
                'type' => 'chain:cfdi',
                'success' => false,
                'message' => 'CFDI OriginalChainGenerator only supports CFDv3.3',
            ];

            return false;
        }

        // Initialize the XSLT processor
        //$processor = new XSLTProcessor; // old method using the XSLTProcessor built-in library (does not support XSL 2.0)
        $factory = new ProcessorFactory(new ArrayCache());
        $processor = $factory->newProcessor();

        // Load the XSLT transformation rules as a DOMDocument
        $xsltFile = PathUtility::join($this->resourceDir, 'CFDI_3_3.xslt');

        $xslt = new DOMDocument();
        $r = $xslt->load($xsltFile);

        if ($r === false) {
            $this->validations[] = [
                'type' => 'chain:cfdi',
                'success' => false,
                'message' => 'Failed to load CFDv3.3 XLST file',
            ];

            return false;
        }

        $r = $processor->importStyleSheet($xslt);

        if ($r === false) {
            $this->validations[] = [
                'type' => 'chain:cfdi',
                'success' => false,
                'message' => 'Failed to load CFDv3.3 XLST into Processor',
            ];

            return false;
        }

        // Everything's loaded, now generate the chain
        return $processor->transformToXML($cfdi->toDOMDocument());
    }

    /**
     * Generate the OriginalChain string for a given FiscalStamp object
     * Returns false on failure
     * @param FiscalStamp $tfd
     * @return string|false
     */
    public function generateForTFD(FiscalStamp $tfd)
    {
        libxml_use_internal_errors(true);
        libxml_clear_errors(); // clean up any previous errors found in other validations


        // Check that the version is correct
        if ($tfd->getVersion() != FiscalStamp::VERSION_1_1) {
            $this->validations[] = [
                'type' => 'chain:tfd',
                'success' => false,
                'message' => 'TFD OriginalChainGenerator only supports TFDv1.1',
            ];

            return false;
        }

        // Initialize the XSLT processor
        //$processor = new XSLTProcessor; // old method using the XSLTProcessor built-in library (does not support XSL 2.0)
        $factory = new ProcessorFactory(new ArrayCache());
        $processor = $factory->newProcessor();


        // Load the XSLT transformation rules as a DOMDocument
        $xsltFile = PathUtility::join($this->resourceDir, 'TFD_1_1.xslt');

        $xslt = new DOMDocument();
        $r = $xslt->load($xsltFile);

        if ($r === false) {
            $this->validations[] = [
                'type' => 'chain:tfd',
                'success' => false,
                'message' => 'Failed to load TFDv1.1 XLST file',
            ];

            return false;
        }

        $r = $processor->importStyleSheet($xslt);

        if ($r === false) {
            $this->validations[] = [
                'type' => 'chain:tfd',
                'success' => false,
                'message' => 'Failed to load TFDv1.1 XLST into Processor',
            ];

            return false;
        }

        // FIXME: debugging this
        print_r($this->libxmlErrors());

        // Everything's loaded, now generate the chain
        return $processor->transformToXML($tfd->toDOMDocument());
    }

    public function getValidations()
    {
        return $this->validations;
    }

    /**
     * @return array
     */
    private function libxmlErrors()
    {
        $errors = libxml_get_errors();
        $result = [];
        foreach ($errors as $error) {
            $result[] = $this->libxmlErrorAsString($error);
        }
        libxml_clear_errors();
        return $result;
    }

    /**
     * @param LibXMLError object $error
     * @return string
     */
    private function libxmlErrorAsString($error)
    {
        /*
        $errorString = "Error $error->code in $error->file (Line:{$error->line}): ";
        $errorString .= trim($error->message);
        */
        return sprintf("Error %d (Line:%d): %s", $error->code, $error->line, trim($error->message));
    }
}