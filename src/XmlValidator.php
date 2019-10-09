<?php

namespace Angle\CFDI;

use LibXMLError;
use DOMDocument;

class XmlValidator
{
    // Relative to the project directory
    const XSD_RESOURCES_DIR = '/resources/xml-schema/';
    const XSD_WHITELIST = [
        'cfdv33.xsd',
        'catCFDI.xsd',
        'tdCFDI.xsd',
        'TimbreFiscalDigitalv11.xsd',
        'Pagos10.xsd',
        'catPagos.xsd',
    ];

    // should be inside the resources directory
    const CFDI_SCHEMA = 'cfdv33.xsd';

    /**
     * Formatted libxml Error details
     * @var array
     */
    public $errors = [];

    /**
     * @var DOMDocument|null
     */
    public $dom;


    public function __construct()
    {
        $libDir = __DIR__ . '/../';

        // Create the Stream Wrapper to manipulate our XSD schema file
        if (in_array(XsdStreamWrapper::PROTOCOL, stream_get_wrappers())) {
            // the stream was previously registered, we'll destroy it and recreate it
            stream_wrapper_unregister(XsdStreamWrapper::PROTOCOL);
        }
        stream_wrapper_register(XsdStreamWrapper::PROTOCOL, XsdStreamWrapper::class, STREAM_IS_URL);

        // Configure our XSD Stream Wrapper
        XsdStreamWrapper::$RESOURCE_DIR = Path::join($libDir, self::XSD_RESOURCES_DIR);
        XsdStreamWrapper::$WHITELIST = self::XSD_WHITELIST;
    }

    public function __destruct()
    {
        stream_wrapper_unregister(XsdStreamWrapper::PROTOCOL);
    }

    /**
     * Validate Incoming Feeds against Listing Schema
     *
     * @param resource $feeds
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function validateXmlFile(string $xmlFilePath)
    {
        // Clear any previous errors
        $this->errors = [];

        if (!class_exists('DOMDocument')) {
            throw new \Exception("'DOMDocument' class not found!");
        }

        if (!file_exists($xmlFilePath)) {
            $this->errors[] = 'Target XML file does not exist';
            throw new \Exception('Target xml file does not exist');
        }

        libxml_use_internal_errors(true);

        try {
            $dom = new DOMDocument();
            $dom->load($xmlFilePath);
        } catch (\Exception $e) {
            $this->errors[] = 'Cannot open target XML file: ' . $e->getMessage();
            $this->errors = array_merge($this->errors, $this->libxmlErrors());
            throw new \Exception('Cannot open target xml file: ' . $e->getMessage());
        }

        return $this->validate($dom);
    }

    public function validateXmlString(string $xmlString)
    {
        // Clear any previous errors
        $this->errors = [];

        if (!class_exists('DOMDocument')) {
            throw new \Exception("'DOMDocument' class not found!");
        }

        libxml_use_internal_errors(true);

        try {
            $dom = new DOMDocument();
            $dom->loadXML($xmlString);
        } catch (\Exception $e) {
            $this->errors[] = 'Cannot load XML string: ' . $e->getMessage();
            $this->errors = array_merge($this->errors, $this->libxmlErrors());
            throw new \Exception('Cannot load XML string: ' . $e->getMessage());
        }

        return $this->validate($dom);
    }

    private function validate(DOMDocument &$dom)
    {
        $schemaUri = XsdStreamWrapper::PROTOCOL . '://' . self::CFDI_SCHEMA;

        try {
            $r = $dom->schemaValidate($schemaUri);
        } catch (\Exception $e) {
            $this->errors = array_merge($this->errors, $this->libxmlErrors());
            throw $e;
        }

        if (!$r) {
            // errors found
            $this->errors = array_merge($this->errors, $this->libxmlErrors());
        }

        if (!empty($this->errors)) {
            return false;
        }

        // the validator was successful, keep the DOM
        $this->dom = $dom;

        return true;
    }

    public function getDOM()
    {
        return $this->dom;
    }

    /**
     * Display Error if Resource is not validated
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
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
}