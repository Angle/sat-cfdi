<?php

namespace Angle\CFDI;

use Angle\CFDI\Utility\PathUtility;

use LibXMLError;
use DOMDocument;
use DOMNode;

use Angle\CFDI\CFDINode;
use Angle\CFDI\CFDIInterface;
use Angle\CFDI\Node\CFDI33\CFDI33;
use Angle\CFDI\Node\CFDI40\CFDI40;

class XmlLoader
{
    // Relative to the project directory
    const XSD_RESOURCES_DIR = '/resources/xml-schema/';

    // This schema file should be inside the resources directory
    const CFDI_3_3_SCHEMA = 'cfdv33.xsd';
    const CFDI_4_0_SCHEMA = 'cfdv40.xsd';

    /**
     * Validations array, in the format: [{type: string, success: true/false, message: string}]
     * @var array
     */
    private $validations = [];

    /**
     * Formatted libxml Error details
     * @var array
     */
    private $errors = [];

    /**
     * @var DOMDocument|null
     */
    private $dom;


    public function __construct()
    {
        $libDir = PathUtility::join(__DIR__, '/../');

        // Create the Stream Wrapper to manipulate our XSD schema file
        if (in_array(XsdStreamWrapper::PROTOCOL, stream_get_wrappers())) {
            // the stream was previously registered, we'll destroy it and recreate it
            stream_wrapper_unregister(XsdStreamWrapper::PROTOCOL);
        }
        stream_wrapper_register(XsdStreamWrapper::PROTOCOL, XsdStreamWrapper::class, STREAM_IS_URL);

        // Configure our XSD Stream Wrapper
        XsdStreamWrapper::$RESOURCE_DIR = PathUtility::join($libDir, self::XSD_RESOURCES_DIR);
    }

    public function __destruct()
    {
        // we'll only unregister the stream if we already had it online
        if (in_array(XsdStreamWrapper::PROTOCOL, stream_get_wrappers())) {
            stream_wrapper_unregister(XsdStreamWrapper::PROTOCOL);
        }
    }


    /**
     * Attempt to parse a CFDI from an XML string.
     * Returns null if the parsing failed
     *
     * @param string $xmlString
     * @return CFDIInterface|null
     */
    public function stringToCFDI(string $xmlString): ?CFDIInterface
    {
        // Clear any previous validations & errors
        $this->validations = [];
        $this->errors = [];

        $r = $this->validateXmlString($xmlString);

        if (!$r) {
            //$errors = implode(' || ', $this->getErrors());
            //throw new \Exception('XML did not validate. [' . $errors . ']');

            // there's no need to throw any more errors nor validations, those should have been raised below
            return null;
        }

        return $this->domToCFDI();
    }

    /**
     * Attempt to parse an XML file.
     * Returns null if the parsing failed
     *
     * @param string $xmlFilePath
     * @return CFDIInterface|null
     */
    public function fileToCFDI(string $xmlFilePath): ?CFDIInterface
    {
        // Clear any previous validations & errors
        $this->validations = [];
        $this->errors = [];

        if (!file_exists($xmlFilePath)) {
            //throw new \Exception('Target xml file does not exist');

            $this->errors[] = 'Target XML file does not exist';

            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'Internal system error [Target XML file does not exist]',
            ];

            return null;
        }


        $r = $this->validateXmlFile($xmlFilePath);

        if (!$r) {
            //$errors = implode(' || ', $this->getErrors());
            //throw new \Exception('XML did not validate. [' . $errors . ']');

            // there's no need to throw any more errors nor validations, those should have been raised below
            return null;
        }

        return $this->domToCFDI();
    }

    private function domToCFDI(): ?CFDIInterface
    {
        $cfdiNode = $this->dom->firstChild;

        try {
            /** @var CFDIInterface $cfdiClass */
            $cfdiClass = self::getCfdiClassFromVersion($cfdiNode);
            $cfdi = $cfdiClass::createFromDOMNode($cfdiNode);

        } catch (CFDIException $e) {
            $this->errors[] = "CFDIException: " . $e->getMessage();

            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'Internal system error [' . $e->getMessage() . ']',
            ];

            return null;
        }

        // Register "warnings" in the validation history
        if ($cfdi->getComplements()) {
            foreach ($cfdi->getComplements() as $c) {
                foreach ($c->getUnknownNodes() as $u) {
                    $this->validations[] = [
                        'type' => 'cfdi',
                        'success' => true,
                        'message' => 'Warning: Unknown Complement node found "' . $u . '"',
                    ];
                }
            }
        }

        // TODO: also register warnings for addendums


        // Keep a copy of the Original XML string
        $cfdi->setOriginalXml($this->dom->saveXML());

        return $cfdi;
    }

    /**
     * @param DOMNode|null $cfdi
     * @return string CFDI Version string
     * @throws CFDIException
     */
    private static function inferCfdiVersion(?DOMNode $cfdi): string
    {
        if ($cfdi === null) {
            throw new CFDIException('Invalid (null) DOMNode provided');
        }

        $version = null;

        if ($cfdi->hasAttributes()) {
            foreach ($cfdi->attributes as $attr) {
                if (strtolower($attr->nodeName) == 'version') {
                    $version = $attr->nodeValue;
                }
            }
        }

        if (!$version) {
            throw new CFDIException('CFDI Version attribute is missing in first child node');
        }

        return $version;
    }

    /**
     * @param DOMNode|null $cfdi
     * @return string CFDI Class name for the specific version
     * @throws CFDIException
     */
    private static function getCfdiClassFromVersion(?DOMNode $cfdi): string
    {
        if ($cfdi === null) {
            throw new CFDIException('Invalid (null) DOMNode provided');
        }

        $version = self::inferCfdiVersion($cfdi);

        if ($version == CFDI33::VERSION_3_3) {
            return CFDI33::class;
        }

        if ($version == CFDI40::VERSION_4_0) {
            return CFDI40::class;
        }


        throw new CFDIException('Unknown Class for CFDI Version: ' . $version);
    }

    /**
     * @param DOMNode|null $cfdi
     * @return string CFDI Schema filepath
     * @throws CFDIException
     */
    private static function getCfdiSchemaFromVersion(?DOMNode $cfdi): string
    {
        if ($cfdi === null) {
            throw new CFDIException('Invalid (null) DOMNode provided');
        }

        $version = self::inferCfdiVersion($cfdi);

        if ($version == CFDI33::VERSION_3_3) {
            return self::CFDI_3_3_SCHEMA;

        } elseif ($version == CFDI40::VERSION_4_0) {
            return self::CFDI_4_0_SCHEMA;
        }

        throw new CFDIException('Unknown Schema for CFDI Version: ' . $version);
    }

    /**
     * Validate a XML file
     *
     * @param string $xmlFilePath
     * @return bool
     */
    private function validateXmlFile(string $xmlFilePath): bool
    {
        if (!class_exists('DOMDocument')) {
            //throw new \Exception("'DOMDocument' class not found!");
            $this->errors[] = "'DOMDocument class not found'";

            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'Internal system error',
            ];

            return false;
        }

        libxml_use_internal_errors(true);
        libxml_clear_errors(); // clean up any previous errors found in other validations

        try {
            $this->dom = new DOMDocument();
            $this->dom->load($xmlFilePath); // we are checking on another previous function that the file exists
        } catch (\Exception $e) {
            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'Error loading basic XML structure',
            ];

            $this->errors[] = 'Cannot open target XML file: ' . $e->getMessage();
            $this->errors = array_merge($this->errors, $this->libxmlErrors());
            //throw new \Exception('Cannot open target xml file: ' . $e->getMessage());
            return false;
        }

        $this->validations[] = [
            'type' => 'xml',
            'success' => true,
            'message' => 'Basic XML structure is valid',
        ];

        return $this->validateDOM();
    }

    /**
     * Validate an XML
     * @param string $xmlString
     * @return bool
     */
    private function validateXmlString(string $xmlString)
    {
        if (!class_exists('DOMDocument')) {
            //throw new \Exception("'DOMDocument' class not found!");
            $this->errors[] = "'DOMDocument class not found'";

            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'Internal system error',
            ];

            return false;
        }

        libxml_use_internal_errors(true);
        libxml_clear_errors(); // clean up any previous errors found in other validations

        try {
            $this->dom = new DOMDocument();
            $this->dom->loadXML($xmlString);
        } catch (\Exception $e) {
            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'Error loading basic XML structure',
            ];

            $this->errors[] = 'Cannot load XML string: ' . $e->getMessage();
            $this->errors = array_merge($this->errors, $this->libxmlErrors());
            //throw new \Exception('Cannot load XML string: ' . $e->getMessage());
            return false;
        }

        $this->validations[] = [
            'type' => 'xml',
            'success' => true,
            'message' => 'Basic XML structure is valid',
        ];

        return $this->validateDOM();
    }

    private function validateDOM()
    {
        // Validate the encoding.
        // We'll also allow "null" encoding, since that usually means the XML does not contain an <?xml> opening tag
        if ($this->dom->encoding === null || strtoupper($this->dom->encoding) == 'UTF-8') {
            $this->validations[] = [
                'type' => 'xml',
                'success' => true,
                'message' => 'XML encoding is UTF-8',
            ];
        } else {
            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'XML encoding is not UTF-8',
            ];

            return false;
        }

        try {
            $cfdiSchema = self::getCfdiSchemaFromVersion($this->dom->firstChild);
        } catch (\Exception $e) {
            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'XML validation error: ' . $e->getMessage(),
            ];

            return false;
        }

        $schemaUri = XsdStreamWrapper::PROTOCOL . '://' . $cfdiSchema;

        try {
            $r = $this->dom->schemaValidate($schemaUri);
        } catch (\Exception $e) {
            $this->errors = array_merge($this->errors, $this->libxmlErrors());

            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'Error validating XML against schema: ' . $cfdiSchema,
            ];

            return false;
        }

        if (!$r) {
            // errors found
            $this->errors = array_merge($this->errors, $this->libxmlErrors());

            $this->validations[] = [
                'type' => 'xml',
                'success' => false,
                'message' => 'XML did not validate against schema: ' . $cfdiSchema,
            ];

            foreach ($this->errors as $e) {
                if (strpos($e, 'Error 3083') === 0) {
                    // Error 3083 refers to the import of XMLSchema files, that is not relevant for the user
                    continue;
                }

                $this->validations[] = [
                    'type' => 'xml:xslt',
                    'success' => false,
                    'message' => $e,
                ];
            }

            return false;
        }

        $this->validations[] = [
            'type' => 'xml',
            'success' => true,
            'message' => 'XML is valid against the official ' . $cfdiSchema . ' schema',
        ];

        return true;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return array
     */
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
            $result[] = sprintf("Error %d (Line:%d): %s", $error->code, $error->line, trim($error->message));
        }
        libxml_clear_errors();
        return $result;
    }
}