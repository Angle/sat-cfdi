<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;
use Angle\CFDI\Node\Complement\CFDIFiscalRegistry;
use Angle\CFDI\Node\Complement\FiscalStamp;
use Angle\CFDI\Node\Complement\Payment\Payments;

use DateTime;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Complement createFromDOMNode(DOMNode $node)
 */
class Complement extends CFDINode
{
    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = "Complemento";

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
    ];



    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var CFDINode[]|array
     */
    protected $complements = [];


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
                // TODO: DOMText
                continue;
            }

            // Note: since we don't know the namespace of the possible Complements, we'll validate against its non-ns name
            switch ($node->nodeName) {
                case FiscalStamp::NODE_NS_NAME:
                    $stamp = FiscalStamp::createFromDOMNode($node);
                    $this->addFiscalStamp($stamp);
                    break;
                case Payments::NODE_NS_NAME:
                    $payments = Payments::createFromDOMNode($node);
                    $this->addComplement($payments);
                    break;
                case CFDIFiscalRegistry::NODE_NS_NAME:
                    $complement = CFDIFiscalRegistry::createFromDOMNode($node);
                    $this->addComplement($complement);
                    break;
                default:
                    throw new CFDIException(sprintf("Unknown children node '%s' in %s", $node->nodeName, self::NODE_NS_NAME));

                // TODO: implement other types of nodes
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

        // Complements node (array)
        foreach ($this->complements as $complement) {
            $complementNode = $complement->toDOMElement($dom);
            $node->appendChild($complementNode);
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

    // none


    #########################
    ## CHILDREN
    #########################

    /**
     * This method will return the first encountered FiscalStamp inside the Complements items
     * @return FiscalStamp
     */
    public function getFiscalStamp(): ?FiscalStamp
    {
        foreach ($this->complements as $c) {
            if ($c instanceof FiscalStamp) {
                return $c;
            }
        }

        return null;
    }



    /**
     * @param FiscalStamp $stamp
     * @throws CFDIException
     * @return Complement
     */
    public function addFiscalStamp(FiscalStamp $stamp): self
    {
        // Check if there is another fiscal stamp
        if ($this->getFiscalStamp() !== null) {
            throw new CFDIException('Cannot add more than one FiscalStamp to the CFDI\'s Complements');
        }
        
        $this->complements[] = $stamp;
        return $this;
    }


    /**
     * Add a generic Complement that implements CFDINode
     * @param CFDINode $node
     * @return Complement
     */
    public function addComplement(CFDINode $node): self
    {
        $this->complements[] = $node;
        return $this;
    }


    /**
     * Get Complements as CFDINode
     * @return CFDINode[]
     */
    public function getComplements(): array
    {
        return $this->complements;
    }

}