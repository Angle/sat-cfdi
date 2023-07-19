<?php

namespace Angle\CFDI\Node\Complement\VehicleSale;

use Angle\CFDI\CFDI33;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static CustomsInformation createFromDOMNode(DOMNode $node)
 */
class CustomsInformation extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "InformacionAduanera";

    const NODE_NS = "ventavehiculos";
    const NODE_NS_URI = "http://www.sat.gob.mx/ventavehiculos";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'number'           => [
            'keywords' => ['numero', 'number'],
            'type'  => CFDINode::ATTR_REQUIRED
        ],
        'date'          => [
            'keywords' => ['fecha', 'date'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'portOfEntry'        => [
            'keywords' => ['aduana', 'portOfEntry'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
    ];

    protected static $children = [];



    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $number;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var string|null
     */
    protected $portOfEntry;

    // no children


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
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * @param string|null $number
     * @return CustomsInformation
     */
    public function setNumber(?string $number): self
    {
        $this->number = $number;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDate(): ?DateTime
    {
        return $this->date;
    }

    /**
     * @param DateTime|string $rawDate
     * @throws CFDIException
     * @return CustomsInformation
     */
    public function setDate($rawDate): self
    {
        if ($rawDate instanceof DateTime) {
            $this->date = $rawDate;
        }

        // sample format: 2023-05-29
        try {
            $tz = new DateTimeZone(CFDINode::DATETIME_TIMEZONE);
            $date = DateTime::createFromFormat(CFDINode::DATE_FORMAT, $rawDate, $tz);
        } catch (\Exception $e) {
            throw new CFDIException('Raw date string is in invalid format, cannot parse stamp date');
        }

        $this->date = $date;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getPortOfEntry(): ?string
    {
        return $this->portOfEntry;
    }

    /**
     * @param string|null $portOfEntry
     * @return CustomsInformation
     */
    public function setPortOfEntry(?string $portOfEntry): self
    {
        $this->portOfEntry = $portOfEntry;
        return $this;
    }

    #########################
    ## CHILDREN
    #########################

    // no children
}