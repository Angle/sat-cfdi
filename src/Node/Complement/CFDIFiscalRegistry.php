<?php

namespace Angle\CFDI\Node\Complement;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * @method static CFDIFiscalRegistry createFromDOMNode(DOMNode $node)
 */
class CFDIFiscalRegistry extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION_1_0 = "1.0";

    const NODE_NAME = "CFDIRegistroFiscal";

    const NODE_NS = "registrofiscal";
    const NODE_NS_URI = "http://www.sat.gob.mx/registrofiscal";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [
        'xmlns:registrofiscal' => "http://www.sat.gob.mx/registrofiscal",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/registrofiscal http://www.sat.gob.mx/sitio_internet/cfd/cfdiregistrofiscal/cfdiregistrofiscal.xsd",
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
        'folio'          => [
            'keywords' => ['Folio', 'folio'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        // PropertyName => ClassName (full namespace)
    ];



    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $version = self::VERSION_1_0;

    /**
     * @var string
     */
    protected $folio;
    

    #########################
    ##     CONSTRUCTOR     ##
    #########################

    // constructor implemented in the CFDINode abstract class

    /**
     * @param DOMNode[]
     * @throws CFDIException
     */
    public function setChildrenFromDOMNodes(array $children): void
    {
        // void
    }


    #########################
    ## CFDI NODE TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElementNS(self::NODE_NS_URI, self::NODE_NS_NAME);

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
     * @return CFDIFiscalRegistry
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
    public function getFolio(): ?string
    {
        return $this->folio;
    }

    /**
     * @param string $folio
     * @return CFDIFiscalRegistry
     */
    public function setFolio(?string $folio): self
    {
        $this->folio = $folio;
        return $this;
    }
}