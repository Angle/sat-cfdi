<?php

namespace Angle\CFDI;

use Angle\CFDI\Utility\PathUtility;

use DOMDocument;
use LibXMLError;

use Genkgo\Xsl\Cache\ArrayCache;
use Genkgo\Xsl\Cache\NullCache;
use Genkgo\Xsl\ProcessorFactory;
use Genkgo\Xsl\XsltProcessor;
use Genkgo\Xsl\Exception\TransformationException;

use Angle\CFDI\CFDI;
use Angle\CFDI\Node\Complement\FiscalStamp;

class OriginalChainGenerator
{
    // Relative to the project directory
    const XSLT_RESOURCES_DIR = '/resources/xslt-processor/';
    const XSLT_WHITELIST = [
        'CFDI_3_3.xslt',
        'TFD_1_1.xslt',
        
        "cfd/2/cadenaoriginal_2_0/utilerias.xslt",
        "cfd/EstadoDeCuentaCombustible/ecc11.xslt",
        "cfd/donat/donat11.xslt",
        "cfd/divisas/divisas.xslt",
        "cfd/implocal/implocal.xslt",
        "cfd/leyendasFiscales/leyendasFisc.xslt",
        "cfd/pfic/pfic.xslt",
        "cfd/TuristaPasajeroExtranjero/TuristaPasajeroExtranjero.xslt",
        "cfd/nomina/nomina12.xslt",
        "cfd/cfdiregistrofiscal/cfdiregistrofiscal.xslt",
        "cfd/pagoenespecie/pagoenespecie.xslt",
        "cfd/aerolineas/aerolineas.xslt",
        "cfd/valesdedespensa/valesdedespensa.xslt",
        "cfd/consumodecombustibles/consumodecombustibles.xslt",
        "cfd/notariospublicos/notariospublicos.xslt",
        "cfd/vehiculousado/vehiculousado.xslt",
        "cfd/servicioparcialconstruccion/servicioparcialconstruccion.xslt",
        "cfd/renovacionysustitucionvehiculos/renovacionysustitucionvehiculos.xslt",
        "cfd/certificadodestruccion/certificadodedestruccion.xslt",
        "cfd/arteantiguedades/obrasarteantiguedades.xslt",
        "cfd/ComercioExterior11/ComercioExterior11.xslt",
        "cfd/ine/ine11.xslt",
        "cfd/iedu/iedu.xslt",
        "cfd/ventavehiculos/ventavehiculos11.xslt",
        "cfd/terceros/terceros11.xslt",
        "cfd/Pagos/Pagos10.xslt",
        "cfd/detallista/detallista.xslt",
        "cfd/EstadoDeCuentaCombustible/ecc12.xslt",
        "cfd/consumodecombustibles/consumodeCombustibles11.xslt",
        "cfd/GastosHidrocarburos10/GastosHidrocarburos10.xslt",
        "cfd/IngresosHidrocarburos10/IngresosHidrocarburos.xslt",
    ];

    // This stylesheet file should be inside the resources directory
    const CFDI_STYLESHEET = 'CFDI_3_3.xslt';
    const TFD_STYLESHEET = 'TFD_1_1.xslt';

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
        $this->resourceDir = realpath(PathUtility::join($libDir, self::XSLT_RESOURCES_DIR));

        // Create the Stream Wrapper to manipulate our XSLT stylesheet file
        if (in_array(XsltStreamWrapper::PROTOCOL, stream_get_wrappers())) {
            // the stream was previously registered, we'll destroy it and recreate it
            stream_wrapper_unregister(XsltStreamWrapper::PROTOCOL);
        }
        stream_wrapper_register(XsltStreamWrapper::PROTOCOL, XsltStreamWrapper::class, STREAM_IS_URL);

        // Configure our XSLT Stream Wrapper
        XsltStreamWrapper::$RESOURCE_DIR = PathUtility::join($libDir, self::XSLT_RESOURCES_DIR);
        XsltStreamWrapper::$WHITELIST   = self::XSLT_WHITELIST;
    }

    public function __destruct()
    {
        stream_wrapper_unregister(XsltStreamWrapper::PROTOCOL);
    }

    /**
     * Generate the OriginalChain string for a given CFDI object
     * Returns false on failure
     * @param CFDI $cfdi
     * @return string|false
     */
    public function generateForCFDI(CFDI $cfdi)
    {
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
        //$factory = new ProcessorFactory(new ArrayCache());
        //$processor = $factory->newProcessor();

        $processor = new XsltProcessor(new NullCache());

        // Load the XSLT transformation rules as a DOMDocument
        $stylesheet = XsltStreamWrapper::PROTOCOL . '://' . self::CFDI_STYLESHEET;

        $xslt = new DOMDocument();
        $r = $xslt->load($stylesheet);

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

        // Everything's loaded, now try to generate the chain
        try {
            $chain = $processor->transformToXML($cfdi->toDOMDocument());
        } catch (TransformationException $t) {
            $this->validations[] = [
                'type' => 'chain:cfdi',
                'success' => false,
                'message' => 'CFDv3.3 XLST ' . $t->getMessage(),
            ];

            return false;
        }

        if ($chain === null) return false;

        return $chain;
    }

    /**
     * Generate the OriginalChain string for a given FiscalStamp object
     * Returns false on failure
     * @param FiscalStamp $tfd
     * @return string|false
     */
    public function generateForTFD(FiscalStamp $tfd)
    {
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
        $factory = new ProcessorFactory(new ArrayCache());
        //$processor = new XsltProcessor(new NullCache());
        $processor = $factory->newProcessor();

        // Load the XSLT transformation rules as a DOMDocument
        $stylesheet = XsltStreamWrapper::PROTOCOL . '://' . self::TFD_STYLESHEET;

        $xslt = new DOMDocument();
        $r = $xslt->load($stylesheet);

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

        // Everything's loaded, now try to generate the chain
        try {
            $chain = $processor->transformToXML($tfd->toDOMDocument());
        } catch (TransformationException $t) {

            $this->validations[] = [
                'type' => 'chain:tfd',
                'success' => false,
                'message' => 'TFD1.1 XLST ' . $t->getMessage(),
            ];

            return false;
        }

        if ($chain === null) return false;

        return $chain;
    }

    public function getValidations()
    {
        return $this->validations;
    }
}