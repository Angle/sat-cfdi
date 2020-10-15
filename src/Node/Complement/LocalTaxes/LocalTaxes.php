<?php

namespace Angle\CFDI\Node\Complement\LocalTaxes;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static LocalTaxes createFromDOMNode(DOMNode $node)
 */
class LocalTaxes extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION_1_0 = "1.0";

    const NODE_NAME = "ImpuestosLocales";

    const NODE_NS = "implocal";
    const NODE_NS_URI = "http://www.sat.gob.mx/implocal";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [
        'xmlns:implocal' => "http://www.sat.gob.mx/implocal",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/implocal http://www.sat.gob.mx/sitio_internet/cfd/implocal/implocal.xsd",
    ];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'version'           => [
            'keywords' => ['version', 'version'],
            'type'  => CFDI::ATTR_REQUIRED
        ],
        'totalRetained'          => [
            'keywords' => ['TotaldeRetenciones', 'totalRetained'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'totalTransferred'        => [
            'keywords' => ['TotaldeTraslados', 'totalTransferred'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        'taxesRetained' => [
            'keywords'  => ['RetencionesLocales', 'taxesRetained', 'retentions'],
            'class'     => LocalTaxesRetained::class,
            'type'      => CFDI::CHILD_ARRAY,
        ],
        'taxesTransferred' => [
            'keywords'  => ['RetencionesLocales', 'taxesTransferred', 'transfers'],
            'class'     => LocalTaxesTransferred::class,
            'type'      => CFDI::CHILD_ARRAY,
        ],
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
    protected $totalRetained;

    /**
     * @var string
     */
    protected $totalTransferred;


    // CHILDREN NODES
    /**
     * @var LocalTaxesRetained[]
     */
    protected $taxesRetained = [];

    /**
     * @var LocalTaxesTransferred[]
     */
    protected $taxesTransferred = [];


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
        foreach ($children as $node) {
            if ($node instanceof DOMText) {
                // TODO: we are skipping the actual text inside the Node.. is this useful?
                continue;
            }

            switch ($node->localName) {
                case LocalTaxesRetained::NODE_NAME:
                    $retained = LocalTaxesRetained::createFromDomNode($node);
                    $this->addLocalTaxesRetained($retained);
                    break;
                case LocalTaxesTransferred::NODE_NAME:
                    $retained = LocalTaxesTransferred::createFromDomNode($node);
                    $this->addLocalTaxesTransferred($retained);
                    break;
                default:
                    throw new CFDIException(sprintf("Unknown children node '%s' in %s", $node->nodeName, self::NODE_NS_NAME));
            }
        }
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

        // Retained Taxes Node
        foreach ($this->taxesRetained as $taxes) {
            $taxesNode = $taxes->toDOMElement($dom);
            $node->appendChild($taxesNode);
        }

        // Transferred Taxes Node
        foreach ($this->taxesTransferred as $taxes) {
            $taxesNode = $taxes->toDOMElement($dom);
            $node->appendChild($taxesNode);
        }

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
     * @return LocalTaxes
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
    public function getTotalRetained(): ?string
    {
        return $this->totalRetained;
    }

    /**
     * @param string $totalRetained
     * @return LocalTaxes
     */
    public function setTotalRetained(?string $totalRetained): self
    {
        $this->totalRetained = $totalRetained;
        return $this;
    }

    /**
     * @return string
     */
    public function getTotalTransferred(): ?string
    {
        return $this->totalTransferred;
    }

    /**
     * @param string $totalTransferred
     * @return LocalTaxes
     */
    public function setTotalTransferred(?string $totalTransferred): self
    {
        $this->totalTransferred = $totalTransferred;
        return $this;
    }
    

    /**
     * @return LocalTaxesRetained[]
     */
    public function getTaxesRetained(): ?array
    {
        return $this->taxesRetained;
    }

    /**
     * @param LocalTaxesRetained $taxes
     * @return LocalTaxes
     */
    public function addLocalTaxesRetained(LocalTaxesRetained $taxes): self
    {
        $this->taxesRetained[] = $taxes;
        return $this;
    }

    /**
     * Alias
     * @param LocalTaxesRetained $taxes
     * @return LocalTaxes
     */
    public function addTaxesRetained(LocalTaxesRetained $taxes): self
    {
        return $this->addLocalTaxesRetained($taxes);
    }

    /**
     * @param LocalTaxesRetained[] $taxes
     * @return LocalTaxes
     */
    public function setTaxesRetained(array $taxes): self
    {
        $this->taxesRetained = $taxes;
        return $this;
    }

    /**
     * @return LocalTaxesTransferred[]
     */
    public function getTaxesTransferred(): ?array
    {
        return $this->taxesTransferred;
    }

    /**
     * @param LocalTaxesTransferred $taxes
     * @return LocalTaxes
     */
    public function addLocalTaxesTransferred(LocalTaxesTransferred $taxes): self
    {
        $this->taxesTransferred[] = $taxes;
        return $this;
    }

    /**
     * Alias
     * @param LocalTaxesTransferred $taxes
     * @return LocalTaxes
     */
    public function addTaxesTransferred(LocalTaxesTransferred $taxes): self
    {
        return $this->addLocalTaxesTransferred($taxes);
    }

    /**
     * @param LocalTaxesTransferred[] $taxes
     * @return LocalTaxes
     */
    public function setTaxesTransferred(array $taxes): self
    {
        $this->taxesTransferred = $taxes;
        return $this;
    }
}