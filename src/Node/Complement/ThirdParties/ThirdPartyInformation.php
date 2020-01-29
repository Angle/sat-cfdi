<?php

namespace Angle\CFDI\Node\Complement\ThirdParties;

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
 * @method static ThirdPartyInformation createFromDOMNode(DOMNode $node)
 */
class ThirdPartyInformation extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "InformacionFiscalTercero";

    const NODE_NS = "terceros";
    const NODE_NS_URI = "http://www.sat.gob.mx/terceros";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'street'           => [
            'keywords' => ['calle', 'street'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'extNumber'           => [
            'keywords' => ['noExterior', 'extNumber'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'intNumber'           => [
            'keywords' => ['noInterior', 'intNumber'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'neighborhood'           => [
            'keywords' => ['colonia', 'neighborhood'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'city'           => [
            'keywords' => ['localidad', 'city'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'reference'           => [
            'keywords' => ['referencia', 'reference'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'county'           => [
            'keywords' => ['municipio', 'county'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'state'           => [
            'keywords' => ['estado', 'state'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'country'           => [
            'keywords' => ['pais', 'country'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'postalCode'           => [
            'keywords' => ['codigoPostal', 'postalCode'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $street;

    /**
     * @var string|null
     */
    protected $extNumber;

    /**
     * @var string|null
     */
    protected $intNumber;

    /**
     * @var string|null
     */
    protected $neighborhood;

    /**
     * @var string|null
     */
    protected $city;

    /**
     * @var string|null
     */
    protected $reference;

    /**
     * @var string
     */
    protected $county;

    /**
     * @var string
     */
    protected $state;

    /**
     * @var string
     */
    protected $country;

    /**
     * @var string
     */
    protected $postalCode;


    // CHILDREN NODES
    // none


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
        foreach ($children as $node) {
            if ($node instanceof DOMText) {
                // TODO: we are skipping the actual text inside the Node.. is this useful?
                continue;
            }

            switch ($node->localName) {
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

        // no children

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
    public function getStreet(): string
    {
        return $this->street;
    }

    /**
     * @param string $street
     * @return ThirdPartyInformation
     */
    public function setStreet(string $street): self
    {
        $this->street = $street;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getExtNumber(): ?string
    {
        return $this->extNumber;
    }

    /**
     * @param string|null $extNumber
     * @return ThirdPartyInformation
     */
    public function setExtNumber(?string $extNumber): self
    {
        $this->extNumber = $extNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getIntNumber(): ?string
    {
        return $this->intNumber;
    }

    /**
     * @param string|null $intNumber
     * @return ThirdPartyInformation
     */
    public function setIntNumber(?string $intNumber): self
    {
        $this->intNumber = $intNumber;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getNeighborhood(): ?string
    {
        return $this->neighborhood;
    }

    /**
     * @param string|null $neighborhood
     * @return ThirdPartyInformation
     */
    public function setNeighborhood(?string $neighborhood): self
    {
        $this->neighborhood = $neighborhood;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     * @return ThirdPartyInformation
     */
    public function setCity(?string $city): self
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @param string|null $reference
     * @return ThirdPartyInformation
     */
    public function setReference(?string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return string
     */
    public function getCounty(): string
    {
        return $this->county;
    }

    /**
     * @param string $county
     * @return ThirdPartyInformation
     */
    public function setCounty(string $county): self
    {
        $this->county = $county;
        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return ThirdPartyInformation
     */
    public function setState(string $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     * @return ThirdPartyInformation
     */
    public function setCountry(string $country): self
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return ThirdPartyInformation
     */
    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    // none.
}