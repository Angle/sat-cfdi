<?php

namespace Angle\CFDI\Node\Complement\FoodVouchers;

use Angle\CFDI\CFDI33;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use Angle\CFDI\Node\Complement\Payment\Payment;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

use DateTime;
use DateTimeZone;

/**
 * @method static Item createFromDOMNode(DOMNode $node)
 */
class Item extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Concepto";

    const NODE_NS = "valesdedespensa";
    const NODE_NS_URI = "http://www.sat.gob.mx/valesdedespensa";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'identifier'           => [
            'keywords' => ['identificador', 'identifier'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'date'          => [
            'keywords' => ['fecha', 'date'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'rfc'        => [
            'keywords' => ['rfc', 'rfc'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'curp'        => [
            'keywords' => ['curp', 'curp'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'name'        => [
            'keywords' => ['nombre', 'name'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'socialSecurityNumber'        => [
            'keywords' => ['numSeguridadSocial', 'socialSecurityNumber'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'amount'        => [
            'keywords' => ['importe', 'amount'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
    ];

    protected static $children = [];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $identifier;

    /**
     * @var DateTime
     */
    protected $date;

    /**
     * @var string
     */
    protected $rfc;

    /**
     * @var string
     */
    protected $curp;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string|null
     */
    protected $socialSecurityNumber;

    /**
     * @var string
     */
    protected $amount;


    // CHILDREN NODES
    // none.


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
    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     * @return self
     */
    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;
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
     * @return Payment
     */
    public function setDate($rawDate): self
    {
        if ($rawDate instanceof DateTime) {
            $this->date = $rawDate;
        }

        // sample format: 2019-09-06T10:09:46
        // TODO: We are assuming that dates ARE in Mexico City's timezone
        try {
            $tz = new DateTimeZone(CFDINode::DATETIME_TIMEZONE);
            $date = DateTime::createFromFormat(CFDINode::DATETIME_FORMAT, $rawDate, $tz);
        } catch (\Exception $e) {
            throw new CFDIException('Raw date string is in invalid format, cannot parse stamp date');
        }

        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getRfc(): string
    {
        return $this->rfc;
    }

    /**
     * @param string $rfc
     * @return self
     */
    public function setRfc(string $rfc): self
    {
        $this->rfc = $rfc;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurp(): string
    {
        return $this->curp;
    }

    /**
     * @param string $curp
     * @return self
     */
    public function setCurp(string $curp): self
    {
        $this->curp = $curp;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSocialSecurityNumber(): ?string
    {
        return $this->socialSecurityNumber;
    }

    /**
     * @param string|null $socialSecurityNumber
     * @return self
     */
    public function setSocialSecurityNumber(?string $socialSecurityNumber): self
    {
        $this->socialSecurityNumber = $socialSecurityNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @param string $amount
     * @return self
     */
    public function setAmount(string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    // no children
}