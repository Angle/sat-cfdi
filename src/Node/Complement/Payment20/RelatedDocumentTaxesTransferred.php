<?php

namespace Angle\CFDI\Node\Complement\Payment20;

use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use Angle\CFDI\Catalog\TaxFactorType;
use Angle\CFDI\Catalog\TaxType;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static RelatedDocumentTaxesTransferred createFromDOMNode(DOMNode $node)
 */
class RelatedDocumentTaxesTransferred extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "TrasladoDR";

    const NODE_NS = "pago20";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;
    const NODE_NS_URI_NAME = self::NODE_NS_URI . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];

    #########################
    ##     ATTRIBUTES      ##
    #########################

    public const ATTR_BASE = 'BaseDR';
    public const ATTR_TAX = 'ImpuestoDR';
    public const ATTR_FACTOR_TYPE = 'TipoFactorDR';
    public const ATTR_RATE = 'TasaOCuotaDR';
    public const ATTR_AMOUNT = 'ImporteDR';


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'base'          => [
            'keywords' => ['BaseDR', 'base'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'tax'          => [
            'keywords' => ['ImpuestoDR', 'tax'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'factorType'        => [
            'keywords' => ['TipoFactorDR', 'factorType'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
        'rate'        => [
            'keywords' => ['TasaOCuotaDR', 'rate'],
            'type' => CFDINode::ATTR_OPTIONAL
        ],
        'amount'        => [
            'keywords' => ['ImporteDR', 'amount'],
            'type' => CFDINode::ATTR_OPTIONAL
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
    protected $base;

    /**
     * @see TaxType
     * @var string
     */
    protected $tax;

    /**
     * @see TaxFactorType
     * @var string
     */
    protected $factorType;

    /**
     * @var string
     */
    protected $rate;

    /**
     * @var string
     */
    protected $amount;


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
    ##   SPECIAL METHODS   ##
    #########################

    public function getTaxName($lang='es'): ?string
    {
        return TaxType::getName($this->tax, $lang);
    }

    public function getFactorTypeName($lang='es'): ?string
    {
        return TaxFactorType::getName($this->factorType, $lang);
    }


    #########################
    ## GETTERS AND SETTERS ##
    #########################
    
    /**
     * @return string
     */
    public function getBase(): ?string
    {
        return $this->base;
    }

    /**
     * @param string|null $base
     * @return RelatedDocumentTaxesTransferred
     */
    public function setBase(?string $base): self
    {
        $this->base = $base;
        return $this;
    }
    
    /**
     * @return string
     */
    public function getTax(): ?string
    {
        return $this->tax;
    }

    /**
     * @param string|null $tax
     * @return RelatedDocumentTaxesTransferred
     */
    public function setTax(?string $tax): self
    {
        // TODO: Use TaxType
        $this->tax = $tax;
        return $this;
    }

    /**
     * @return string
     */
    public function getFactorType(): ?string
    {
        return $this->factorType;
    }

    /**
     * @param string|null $factorType
     * @return RelatedDocumentTaxesTransferred
     */
    public function setFactorType(?string $factorType): self
    {
        // TODO: use TaxFactorType
        $this->factorType = $factorType;
        return $this;
    }

    /**
     * @return string
     */
    public function getRate(): ?string
    {
        return $this->rate;
    }

    /**
     * @param string|null $rate
     * @return RelatedDocumentTaxesTransferred
     */
    public function setRate(?string $rate): self
    {
        $this->rate = $rate;
        return $this;
    }

    /**
     * @return string
     */
    public function getAmount(): ?string
    {
        return $this->amount;
    }

    /**
     * @param string|null $amount
     * @return RelatedDocumentTaxesTransferred
     */
    public function setAmount(?string $amount): self
    {
        $this->amount = $amount;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    // no children
}