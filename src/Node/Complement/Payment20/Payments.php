<?php

namespace Angle\CFDI\Node\Complement\Payment20;

use Angle\CFDI\CFDIException;

use Angle\CFDI\CFDINode;

use Angle\CFDI\Node\Complement\PaymentInterface;
use DateTime;
use DateTimeZone;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static Payments createFromDOMNode(DOMNode $node)
 */
class Payments extends CFDINode implements PaymentInterface
{
    #########################
    ##        PRESETS      ##
    #########################

    const VERSION_2_0 = "2.0";

    const NODE_NAME = "Pagos";

    const NODE_NS = "pago20";
    const NODE_NS_URI = "http://www.sat.gob.mx/Pagos20";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    protected static $baseAttributes = [
        'xmlns:pago20' => "http://www.sat.gob.mx/Pagos20",
        'xmlns:xsi' => "http://www.w3.org/2001/XMLSchema-instance",
        'xsi:schemaLocation' => "http://www.sat.gob.mx/Pagos20 http://www.sat.gob.mx/sitio_internet/cfd/Pagos/Pagos20.xsd",
    ];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // PropertyName => [spanish (official SAT), english]
        'version'           => [
            'keywords' => ['Version', 'version'],
            'type' => CFDINode::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        'payments' => [
            'keywords'  => ['Pago', 'payment'],
            'class'     => Payment::class,
            'type'      => CFDINode::CHILD_ARRAY,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * @var string
     */
    protected $version = self::VERSION_2_0;


    // CHILDREN NODES
    /**
     * @var Totals|null
     */
    protected $totals;

    /**
     * @var Payment[]
     */
    protected $payments = [];


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
                case Totals::NODE_NAME:
                    $totals = Totals::createFromDomNode($node);
                    $this->setTotals($totals);
                    break;
                case Payment::NODE_NAME:
                    $payment = Payment::createFromDomNode($node);
                    $this->addPayment($payment);
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

        // TransferredList Node
        if ($this->totals) {
            // This can be null, no problem if not found
            $totalsNode = $this->totals->toDOMElement($dom);
            $node->appendChild($totalsNode);
        }

        // Payments Node
        foreach ($this->payments as $payment) {
            $paymentNode = $payment->toDOMElement($dom);
            $node->appendChild($paymentNode);
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
     * @return Payments
     */
    public function setVersion(?string $version): self
    {
        // Note: this value is fixed, it cannot be set or changed
        //$this->version = $version;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return Totals|null
     */
    public function getTotals(): ?Totals
    {
        return $this->totals;
    }

    /**
     * @param Totals|null $totals
     */
    public function setTotals(?Totals $totals): void
    {
        $this->totals = $totals;
    }

    /**
     * @return Payment[]
     */
    public function getPayments(): ?array
    {
        return $this->payments;
    }

    /**
     * @param Payment $payment
     * @return Payments
     */
    public function addPayment(Payment $payment): self
    {
        $this->payments[] = $payment;
        return $this;
    }

    /**
     * @param Payment[] $payments
     * @return Payments
     */
    public function setPayments(array $payments): self
    {
        $this->payments = $payments;
        return $this;
    }

}