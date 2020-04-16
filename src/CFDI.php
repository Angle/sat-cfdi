<?php

namespace Angle\CFDI;

use Angle\CFDI\Catalog\PaymentType;
use Angle\CFDI\CFDIException;
use Angle\CFDI\Node\TaxesRetained;
use Angle\CFDI\Node\TaxesRetainedList;
use Angle\CFDI\Node\TaxesTransferred;
use Angle\CFDI\Node\TaxesTransferredList;
use Angle\CFDI\Utility\Math;
use Angle\CFDI\Utility\PathUtility;

use Angle\CFDI\Node\Issuer;
use Angle\CFDI\Node\Recipient;
use Angle\CFDI\Node\ItemList;
use Angle\CFDI\Node\RelatedCFDIList;
use Angle\CFDI\Node\Taxes;

use Angle\CFDI\Node\Complement;
use Angle\CFDI\Node\Complement\FiscalStamp;
use Angle\CFDI\Node\Complement\Payment\Payments;
use Angle\CFDI\Node\Complement\LocalTaxes\LocalTaxes;

use Angle\CFDI\Node\Addendum;

use DateTime;
use DateTimeZone;
use RuntimeException;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMText;

/**
 * @method static CFDI createFromDOMNode(DOMNode $node)
 */
class CFDI extends CFDINode
{
    #########################
    ##       CATALOG       ##
    #########################

    const VERSION_3_3 = "3.3";
    const DATETIME_FORMAT = 'Y-m-d\TH:i:s';
    const DATETIME_TIMEZONE = 'America/Mexico_City';

    const ATTR_REQUIRED = 'R';
    const ATTR_OPTIONAL = 'O';

    const CHILD_UNIQUE  = 'U';
    const CHILD_ARRAY   = 'A';

    #########################
    ##        PRESETS      ##
    #########################

    const NODE_NAME = 'Comprobante';

    const NODE_NS = "cfdi";
    const NODE_NS_URI = "http://www.sat.gob.mx/cfd/3";
    const NODE_NS_NAME = self::NODE_NS . ":" . self::NODE_NAME;

    const SERIES_WRONG_LENGTH_ERROR = 1;


    protected static $baseAttributes = [
        'xmlns:cfdi'            => 'http://www.sat.gob.mx/cfd/3',
        'xmlns:xsi'             => 'http://www.w3.org/2001/XMLSchema-instance',
        'xsi:schemaLocation'    => 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd',
    ];


    #########################
    ## PROPERTY NAME TRANSLATIONS ##
    #########################

    protected static $attributes = [
        // keywords => [spanish (official SAT), english]
        'version'           => [
            'keywords' => ['Version', 'version'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'series'            => [
            'keywords' => ['Serie', 'series'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'folio'             => [
            'keywords' => ['Folio', 'folio'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'date'              => [
            'keywords' => ['Fecha', 'date'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'paymentMethod'     => [
            'keywords' => ['FormaPago', 'paymentMethod'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'paymentConditions'     => [
            'keywords' => ['CondicionesDePago', 'paymentConditions'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'subTotal'          => [
            'keywords' => ['SubTotal', 'subTotal'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'discount'          => [
            'keywords' => ['Descuento', 'discount'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'currency'          => [
            'keywords' => ['Moneda', 'currency'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'exchangeRate'      => [
            'keywords' => ['TipoCambio', 'exchangeRate'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'total'             => [
            'keywords' => ['Total', 'total'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'cfdiType'       => [
            'keywords' => ['TipoDeComprobante', 'cfdiType'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'paymentType'       => [
            'keywords' => ['MetodoPago', 'paymentType'],
            'type' => CFDI::ATTR_OPTIONAL
        ],
        'postalCode'        => [
            'keywords' => ['LugarExpedicion', 'postalCode'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'signature'         => [
            'keywords' => ['Sello', 'signature'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'certificateNumber' => [
            'keywords' => ['NoCertificado', 'certificateNumber'],
            'type' => CFDI::ATTR_REQUIRED
        ],
        'certificate'       => [
            'keywords' => ['Certificado', 'certificate'],
            'type' => CFDI::ATTR_REQUIRED
        ],
    ];

    protected static $children = [
        'relatedCFDIList' => [
            'keywords'  => ['CfdiRelacionados', 'relatedCFDIList'],
            'class'     => RelatedCFDIList::class,
            'type'      => CFDI::CHILD_UNIQUE,
        ],
        'issuer' => [
            'keywords'  => ['Emisor', 'issuer'],
            'class'     => Issuer::class,
            'type'      => CFDI::CHILD_UNIQUE,
        ],
        'recipient' => [
            'keywords'  => ['Receptor', 'recipient'],
            'class'     => Recipient::class,
            'type'      => CFDI::CHILD_UNIQUE,
        ],
        'itemList' => [
            'keywords'  => ['Conceptos', 'itemList'],
            'class'     => ItemList::class,
            'type'      => CFDI::CHILD_UNIQUE,
        ],
        'taxes' => [
            'keywords'  => ['Impuestos', 'taxes'],
            'class'     => Taxes::class,
            'type'      => CFDI::CHILD_UNIQUE,
        ],
        'complements' => [
            'keywords'  => ['Complemento', 'complements'],
            'class'     => Complement::class,
            'type'      => CFDI::CHILD_ARRAY,
        ],
    ];


    #########################
    ##      PROPERTIES     ##
    #########################

    /**
     * Display Label: Version
     * Required
     * Fixed Value: "3.3"
     * No whitespace
     * @var string
     */
    protected $version = CFDI::VERSION_3_3;

    /**
     * Display Label: Serie
     * Optional
     * MinLength = 1, MaxLength = 25
     * XSD Pattern: [^|]{1,25}
     * RegExp = ^[a-zA-Z0-9]$
     * No whitespace
     * @var string|null
     */
    protected $series;

    /**
     * Display Label: Folio
     * Optional
     * MinLength = 1, MaxLength = 40
     * XSD Pattern: [^|]{1,40}
     * RegExp = ^[a-zA-Z0-0]$
     * No whitespace
     * @var string|null
     */
    protected $folio;

    /**
     * Display Label: Fecha
     * Required
     * XSD Pattern: ((19|20)[0-9][0-9])-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])
     * RegExp =
     * No whitespace
     * @var DateTime|null
     */
    protected $date;

    /**
     * @var string
     */
    protected $paymentMethod; // Forma de Pago

    /**
     * @var string
     */
    protected $subTotal;

    /**
     * @var string
     */
    protected $discount;

    /**
     * @var string
     */
    protected $currency;

    /**
     * @var string
     */
    protected $exchangeRate;

    /**
     * @var string
     */
    protected $total;


    /**
     * @var string
     */
    protected $cfdiType; // Tipo de Comprobante

    /**
     * @var string
     * @see PaymentType
     */
    protected $paymentType; // Método de Pago

    /**
     * @var string
     */
    protected $paymentConditions; // Condiciones de Pago

    /**
     * @var string
     */
    protected $postalCode; // Lugar de Expedición


    /**
     * @var string
     */
    protected $signature; // Sello

    /**
     * @var string
     */
    protected $certificateNumber;

    /**
     * @var string
     */
    protected $certificate; // (public key)

    /**
     * @var string
     */
    protected $confirmation;



    // CHILDREN NODES

    /**
     * @var RelatedCFDIList|null
     */
    protected $relatedCFDIList;

    /**
     * @var Issuer
     */
    protected $issuer;

    /**
     * @var Recipient
     */
    protected $recipient;

    /**
     * @var ItemList
     */
    protected $itemList;

    /**
     * @var Taxes|null
     */
    protected $taxes;

    /**
     * @var Complement[]
     */
    protected $complements = [];


    // TODO: Addendum


    #########################
    ##     CONSTRUCTOR     ##
    #########################

    // constructor implemented in the CFDINode abstract class
    public function __construct(array $data)
    {
        parent::__construct($data);
        $this->calculateTaxesAndTotals();
        $this->cleanUpValuesAndEmptyProperties();
    }

    /**
     * @param DOMNode[]
     * @throws CFDIException
     */
    public function setChildrenFromDOMNodes(array $children): void
    {
        foreach ($children as $node) {
            if ($node instanceof DOMText) {
                // TODO: we are skipping the actual text inside the Node.. is this useful?
                // TODO: DOMText
                continue;
            }

            switch ($node->localName) {
                case RelatedCFDIList::NODE_NAME:
                    $relatedCFDIList = RelatedCFDIList::createFromDOMNode($node);
                    $this->setRelatedCFDIList($relatedCFDIList);
                    break;
                case Issuer::NODE_NAME:
                    $issuer = Issuer::createFromDOMNode($node);
                    $this->setIssuer($issuer);
                    break;
                case Recipient::NODE_NAME:
                    $recipient = Recipient::createFromDOMNode($node);
                    $this->setRecipient($recipient);
                    break;
                case ItemList::NODE_NAME:
                    $itemList = ItemList::createFromDOMNode($node);
                    $this->setItemList($itemList);
                    break;
                case Taxes::NODE_NAME:
                    $taxes = Taxes::createFromDOMNode($node);
                    $this->setTaxes($taxes);
                    break;
                case Complement::NODE_NAME:
                    $complement = Complement::createFromDOMNode($node);
                    $this->addComplement($complement);
                    break;
                case Addendum::NODE_NAME:
                    // TODO: implement Addendum
                    break;
                default:
                    //throw new CFDIException(sprintf("Unknown children node '%s' in %s", $node->nodeName, self::NODE_NS_NAME));
            }
        }
    }


    #########################
    ## CFDI TO DOM TRANSLATION
    #########################

    public function toDOMElement(DOMDocument $dom): DOMElement
    {
        $node = $dom->createElementNS(self::NODE_NS_URI, self::NODE_NS_NAME);

        foreach ($this->getAttributes() as $attr => $value) {
            $node->setAttribute($attr, $value);
        }

        // RelatedCFDIList Node
        if ($this->relatedCFDIList) {
            // This can be null, no problem if not found
            $relatedCFDIListNode = $this->relatedCFDIList->toDOMElement($dom);
            $node->appendChild($relatedCFDIListNode);
        }

        // Issuer Node
        if ($this->issuer) {
            // TODO: What happens if the issuer is not set?
            $issuerNode = $this->issuer->toDOMElement($dom);
            $node->appendChild($issuerNode);
        }

        // Recipient Node
        if ($this->recipient) {
            // TODO: What happens if the recipient is not set?
            $recipientNode = $this->recipient->toDOMElement($dom);
            $node->appendChild($recipientNode);
        }

        // ItemList Node
        if ($this->itemList) {
            // TODO: What happens if the itemList is not set?
            $itemListNode = $this->itemList->toDOMElement($dom);
            $node->appendChild($itemListNode);
        }

        // Taxes Node
        if ($this->taxes) {
            // TODO: What happens if the taxes is not set?
            $taxesNode = $this->taxes->toDOMElement($dom);
            $node->appendChild($taxesNode);
        }

        // Complements Node
        foreach ($this->complements as $complement) {
            $complementNode = $complement->toDOMElement($dom);
            $node->appendChild($complementNode);
        }

        return $node;
    }


    #########################
    ## CFDI TO XML
    #########################

    public function toDOMDocument(): DOMDocument
    {
        $dom = new \DOMDocument('1.0','UTF-8');
        $dom->preserveWhiteSpace = false;

        $cfdiNode = $this->toDOMElement($dom);
        $dom->appendChild($cfdiNode);

        return $dom;
    }

    // TODO: DOMDocument duplicates the Namespace declarations of any child
    public function toXML(): string
    {
        return $this->toDOMDocument()->saveXML();
    }


    #########################
    ## VALIDATION
    #########################

    public function validate(): bool
    {
        // TODO: implement the full set of validation, including type and Business Logic

        if (!($this->date instanceof DateTime)) {
            return false;
        }

        return true;
    }

    /**
     * DEPRECATED: Use the OriginalChainGenerator instead, it employs a robust XLS transpiler
     * @deprecated
     *
     * Builds the Original Chain Sequence for the CFDI
     * Returns false on failure
     * @return string|false
     */
    public function getChainSequence(): string
    {
        $items = [];

        // 1. Comprobante
        $items[] = $this->version;

        if ($this->series) $items[] = $this->series;
        if ($this->folio) $items[] = $this->folio;

        if (!($this->date instanceof DateTime)) {
            //throw new CFDIException('Date is not a valid DateTime');
            return false;
        }

        $items[] = $this->date->format(CFDI::DATETIME_FORMAT);

        if ($this->paymentMethod) $items[] = $this->paymentMethod;

        $items[] = $this->certificateNumber;

        if ($this->paymentConditions) $items[] = $this->paymentConditions;

        $items[] = $this->subTotal;

        if ($this->discount) $items[] = $this->discount;

        $items[] = $this->currency;

        if ($this->exchangeRate) $items[] = $this->exchangeRate;

        $items[] = $this->total;
        $items[] = $this->cfdiType;

        if ($this->paymentType) $items[] = $this->paymentType;

        $items[] = $this->postalCode;

        if ($this->confirmation) $items[] = $this->confirmation;


        // 2. CFDIRelacionados
        if ($this->relatedCFDIList) {
            $items[] = $this->relatedCFDIList->getType();

            foreach ($this->relatedCFDIList->getRelated() as $related) {
                $items[] = $related->getUuid();
            }
        }

        // 3. Emisor
        if (!$this->issuer) {
            // An issuer is required in the CFDI
            return false;
        }

        $items[] = $this->issuer->getRfc();

        if ($this->issuer->getName()) $items[] = $this->issuer->getName();

        $items[] = $this->issuer->getRegime();


        // 4. Receptor
        if (!$this->recipient) {
            // A recipient is required in the CFDI
            return false;
        }

        $items[] = $this->recipient->getRfc();

        if ($this->recipient->getName()) $items[] = $this->recipient->getName();
        if ($this->recipient->getForeignCountry()) $items[] = $this->recipient->getForeignCountry();
        if ($this->recipient->getForeignTaxCode()) $items[] = $this->recipient->getForeignTaxCode();

        $items[] = $this->recipient->getCfdiUse();


        // 5. Conceptos
        if (!$this->itemList) {
            // An item list is required in the CFDI
            return false;
        }

        foreach ($this->itemList->getItems() as $it) {
            $items[] = $it->getCode();
            if ($it->getId()) $items[] = $it->getId();
            $items[] = $it->getQuantity();
            $items[] = $it->getUnitCode();
            if ($it->getUnit()) $items[] = $it->getUnit();
            $items[] = $it->getDescription();
            $items[] = $it->getUnitPrice();
            $items[] = $it->getAmount();
            if ($it->getDiscount()) $items[] = $it->getDiscount();


            if ($it->getTaxes() && $it->getTaxes()->getTransferredList()) {
                foreach ($it->getTaxes()->getTransferredList()->getTransfers() as $tax) {
                    $items[] = $tax->getBase();
                    $items[] = $tax->getTax();
                    $items[] = $tax->getFactorType();
                    if ($tax->getRate()) $items[] = $tax->getRate();
                    if ($tax->getAmount()) $items[] = $tax->getAmount();
                }
            }

            if ($it->getTaxes() && $it->getTaxes()->getRetainedList()) {
                foreach ($it->getTaxes()->getRetainedList()->getRetentions() as $tax) {
                    $items[] = $tax->getBase();
                    $items[] = $tax->getTax();
                    $items[] = $tax->getFactorType();
                    $items[] = $tax->getRate();
                    $items[] = $tax->getAmount();
                }
            }

            foreach ($it->getCustomsInformation() as $customs) {
                $items[] = $customs->getImportDocumentNumber();
            }

            if ($it->getPropertyTaxAccount()) {
                $items[] = $it->getPropertyTaxAccount()->getNumber();
            }

            // TODO: Item Complements

            foreach ($it->getParts() as $part) {
                $items[] = $part->getCode();
                if ($part->getId()) $items[] = $part->getId();
                $items[] = $part->getQuantity();
                if ($part->getUnit()) $items[] = $part->getUnit();
                $items[] = $part->getDescription();
                if ($part->getUnitPrice()) $items[] = $part->getUnitPrice();
                if ($part->getAmount()) $items[] = $part->getAmount();

                foreach ($part->getCustomsInformation() as $customs) {
                    $items[] = $customs->getImportDocumentNumber();
                }
            }
        }


        // 6-9. Impuestos
        if ($this->taxes) {
            // 6. Impuestos:Retención
            if ($this->taxes->getRetainedList()) {
                foreach ($this->taxes->getRetainedList()->getRetentions() as $ret) {
                    $items[] = $ret->getTax();
                    $items[] = $ret->getAmount();
                }
            }

            // 7. Impuestos: TotalRetención
            if ($this->taxes->getTotalRetainedAmount()) $items[] = $this->taxes->getTotalRetainedAmount();

            // 8. Impuestos:Traslado
            if ($this->taxes->getTransferredList()) {
                foreach ($this->taxes->getTransferredList()->getTransfers() as $tra) {
                    $items[] = $tra->getTax();
                    $items[] = $tra->getFactorType();
                    $items[] = $tra->getRate();
                    $items[] = $tra->getAmount();
                }
            }

            // 9. Impuestos: TotalTrasladados
            if ($this->taxes->getTotalTransferredAmount()) $items[] = $this->taxes->getTotalTransferredAmount();
        }

        // 12. Complement

        // TODO: Complement


        // Prepare the items to be written as a string
        $items = array_map('Angle\CFDI\CFDI::cleanWhitespace', $items);

        return '||' . implode('|', $items) . '||';
    }


    #########################
    ##   SPECIAL METHODS   ##
    #########################

    /**
     * This will return the first UUID found in the Complements
     */
    public function getUuid(): ?string
    {
        foreach ($this->complements as $complement) {
            if ($complement->getFiscalStamp()) {
                if ($complement->getFiscalStamp()->getUuid()) {
                    return $complement->getFiscalStamp()->getUuid();
                }
            }
        }

        // nothing found
        return null;
    }

    /**
     * This will return the first FiscalStamp found in the Complements
     */
    public function getFiscalStamp(): ?FiscalStamp
    {
        foreach ($this->complements as $complement) {
            if ($complement->getFiscalStamp() instanceof FiscalStamp) {
                return $complement->getFiscalStamp();
            }
        }

        // nothing found
        return null;
    }

    /**
     * This will return the first LocalTaxes found in the Complements
     */
    public function getLocalTaxes(): ?LocalTaxes
    {
        foreach ($this->complements as $complement) {
            if ($complement->getLocalTaxes() instanceof LocalTaxes) {
                return $complement->getLocalTaxes();
            }
        }

        // nothing found
        return null;
    }

    /**
     * This will return the first PaymentComplement found in the Complements
     */
    public function getPaymentComplement(): ?Payments
    {
        foreach ($this->complements as $complement) {
            if ($complement->getPayment() instanceof Payments) {
                return $complement->getPayment();
            }
        }

        // nothing found
        return null;
    }

    /**
     * This will return the first Complement of the given class
     * @param string $class classname
     * @return CFDINode|null
     */
    public function getComplement($class): ?CFDINode
    {
        foreach ($this->complements as $complement) {
            foreach ($complement->getComplements() as $node) {
                if ($node instanceof $class) {
                    return $node;
                }
            }
        }

        // nothing found
        return null;
    }

    /**
     * Calculate and update the CFDI's subtotal and totals for items and taxes
     * This method has to be called _after_ buildTaxes
     */
    public function calculateTaxesAndTotals(): void
    {
        $subtotal = '0';
        $discount = '0';

        // Initialize variables to hold the Taxes
        $transfers = [];
        $retentions = [];

        $totalTransferredAmount = '0';
        $totalRetainedAmount = '0';

        foreach ($this->itemList->getItems() as $it) {

            $subtotal = Math::add($subtotal, $it->getAmount());

            $itemDiscount = $it->getDiscount() ?? '0';
            $discount = Math::add($discount, $itemDiscount);

            // Process any Transferred taxes
            if ($it->getTaxes() && $it->getTaxes()->getTransferredList()) {
                foreach ($it->getTaxes()->getTransferredList()->getTransfers() as $tax) {
                    $key = $tax->getTax() . '-' . $tax->getFactorType() . '-' . $tax->getRate();

                    if (!array_key_exists($key, $transfers)) {
                        $transfers[$key] = [
                            'tax' => $tax->getTax(),
                            'factorType' => $tax->getFactorType(),
                            'rate' => $tax->getRate(),
                            'amount' => '0',
                        ];
                    }

                    $taxAmount = $tax->getAmount() ?? '0';
                    $transfers[$key]['amount'] = Math::add($transfers[$key]['amount'], $taxAmount);
                    $totalTransferredAmount = Math::add($totalTransferredAmount, $taxAmount);
                }
            }

            // Process any Retained taxes
            if ($it->getTaxes() && $it->getTaxes()->getRetainedList()) {
                foreach ($it->getTaxes()->getRetainedList()->getRetentions() as $tax) {
                    $key = $tax->getTax();

                    if (!array_key_exists($key, $retentions)) {
                        $retentions[$key] = [
                            'tax' => $key,
                            'amount' => '0',
                        ];
                    }

                    $taxAmount = $tax->getAmount() ?? '0';
                    $retentions[$key]['amount'] = Math::add($retentions[$key]['amount'], $taxAmount);
                    $totalRetainedAmount = Math::add($totalRetainedAmount, $taxAmount);
                }
            }

            // TODO: Local Taxes..
        }

        // Purge the taxes that amount to 0
        foreach ($transfers as $k => $t) {
            if (Math::equal($t['amount'], '0')) {
                unset($transfers[$k]);
            }
        }
        foreach ($retentions as $k => $t) {
            if (Math::equal($t['amount'], '0')) {
                unset($retentions[$k]);
            }
        }

        // Calculate the Total Amount
        $total = $subtotal;
        $total = Math::sub($total, $discount);
        $total = Math::add($total, $totalTransferredAmount);
        $total = Math::sub($total, $totalRetainedAmount);

        // Update the CFDI object
        $this->setSubTotal($subtotal);
        $this->setDiscount($discount);
        $this->setTotal($total);


        // Build the Taxes node
        $this->taxes = new Taxes([]);
        $this->taxes->setTotalTransferredAmount(Math::round($totalTransferredAmount,2));
        $this->taxes->setTotalRetainedAmount(Math::round($totalRetainedAmount,2));

        $transferredList = new TaxesTransferredList([]);
        foreach ($transfers as $k => $t) {
            $tax = new TaxesTransferred($t);
            $transferredList->addTransfer($tax);
        }
        $this->taxes->setTransferredList($transferredList);


        $retentionList = new TaxesRetainedList([]);
        foreach ($retentions as $k => $t) {
            $tax = new TaxesRetained($t);
            $retentionList->addRetention($tax);
        }
        $this->taxes->setRetainedList($retentionList);
    }

    public function cleanUpValuesAndEmptyProperties()
    {
        $this->total = Math::round($this->total, 2);
        $this->subTotal = Math::round($this->subTotal, 2);

        if (Math::equal($this->discount, '0')) {
            $this->discount = null;
        } else {
            $this->discount = Math::round($this->discount, 2);
        }

        // Check each item
        foreach ($this->itemList->getItems() as $it) {

            $it->setQuantity( Math::round($it->getQuantity(), 6));

            $it->setAmount( Math::round($it->getAmount(), 2));
            $it->setUnitPrice( Math::round($it->getUnitPrice(), 2));

            if (Math::equal($it->getDiscount(), '0')) {
                $it->setDiscount(null);
            } else {
                $it->setDiscount( Math::round($this->discount, 2) );
            }

            // Clean up the object in case there are no Transfers
            if ($it->getTaxes() && $it->getTaxes()->getTransferredList()
                && empty($it->getTaxes()->getTransferredList()->getTransfers())) {
                $it->getTaxes()->setTransferredList(null);
            } else {
                foreach ($it->getTaxes()->getTransferredList()->getTransfers() as $t) {
                    $t->setAmount(Math::round($t->getAmount(), 2));
                }
            }

            // Clean up the object in case there are no Retentions
            if ($it->getTaxes() && $it->getTaxes()->getRetainedList()
                && empty($it->getTaxes()->getRetainedList()->getRetentions())) {
                $it->getTaxes()->setRetainedList(null);
            } else {
                foreach ($it->getTaxes()->getRetainedList()->getRetentions() as $t) {
                    $t->setAmount(Math::round($t->getAmount(), 2));
                }
            }
        }


        // GLOBAL TAXES
        $this->taxes->setTotalTransferredAmount(Math::round($this->taxes->getTotalTransferredAmount(),2));
        $this->taxes->setTotalRetainedAmount(Math::round($this->taxes->getTotalRetainedAmount(),2));

        // Check the global (total) taxes
        if (empty($this->getTaxes()->getTransferredList()->getTransfers())) {
            $this->getTaxes()->setTransferredList(null);
        } else {
            foreach ($this->getTaxes()->getTransferredList()->getTransfers() as $t) {
                $t->setAmount(Math::round($t->getAmount(), 2));
            }
        }

        if (empty($this->getTaxes()->getRetainedList()->getRetentions())) {
            $this->getTaxes()->setRetainedList(null);
        } else {
            foreach ($this->getTaxes()->getRetainedList()->getRetentions() as $t) {
                $t->setAmount(Math::round($t->getAmount(), 2));
            }
        }
    }


    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string|null
     */
    public function getVersion(): ?string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return CFDI
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
    public function getSeries(): ?string
    {
        return $this->series;
    }

    /**
     * @param string $series
     * @return CFDI
     * @throws CFDIException
     */
    public function setSeries(?string $series): self
    {
        // Validate Length
        $l = strlen($series);

        if ($l < 1 || $l > 25) {
            throw new CFDIException("Series contains wrong length.", self::SERIES_WRONG_LENGTH_ERROR);
        }

        // Validate contents
        if (preg_match('/^[a-zA-Z0-0]$/', $series)) {

        }



        $this->series = $series;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFolio(): ?string
    {
        return $this->folio;
    }

    /**
     * @param string|null $folio
     * @return CFDI
     */
    public function setFolio(?string $folio): self
    {
        $this->folio = $folio;
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
     * @return CFDI
     */
    public function setDate($rawDate): self
    {
        if ($rawDate instanceof DateTime) {
            $this->date = $rawDate;
            return $this;
        }

        // sample format: 2019-09-06T10:09:46
        // TODO: We are assuming that dates ARE in Mexico City's timezone
        try {
            $tz = new DateTimeZone(CFDI::DATETIME_TIMEZONE);
            $date = DateTime::createFromFormat(CFDI::DATETIME_FORMAT, $rawDate, $tz);
        } catch (\Exception $e) {
            throw new CFDIException('Raw date string is in invalid format, cannot parse date');
        }

        $this->date = $date;

        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentMethod(): ?string
    {
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     * @return CFDI
     */
    public function setPaymentMethod(?string $paymentMethod): self
    {
        $this->paymentMethod = $paymentMethod;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubTotal(): ?string
    {
        return $this->subTotal;
    }

    /**
     * @param string $subTotal
     * @return CFDI
     */
    public function setSubTotal(?string $subTotal): self
    {
        $this->subTotal = $subTotal;
        return $this;
    }

    /**
     * @return string
     */
    public function getDiscount(): ?string
    {
        return $this->discount;
    }

    /**
     * @param string $discount
     * @return CFDI
     */
    public function setDiscount(?string $discount): self
    {
        $this->discount = $discount;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     * @return CFDI
     */
    public function setCurrency(?string $currency): self
    {
        $this->currency = $currency;
        return $this;
    }

    /**
     * @return string
     */
    public function getExchangeRate(): ?string
    {
        return $this->exchangeRate;
    }

    /**
     * @param string $exchangeRate
     * @return CFDI
     */
    public function setExchangeRate(?string $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    /**
     * @return string
     */
    public function getTotal(): ?string
    {
        return $this->total;
    }

    /**
     * @param string $total
     * @return CFDI
     */
    public function setTotal(?string $total): self
    {
        $this->total = $total;
        return $this;
    }

    /**
     * @return string
     */
    public function getCfdiType(): ?string
    {
        return $this->cfdiType;
    }

    /**
     * @param string $cfdiType
     * @return CFDI
     */
    public function setCfdiType(?string $cfdiType): self
    {
        $this->cfdiType = $cfdiType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentType(): ?string
    {
        return $this->paymentType;
    }

    /**
     * @param string $paymentType
     * @return CFDI
     */
    public function setPaymentType(?string $paymentType): self
    {
        $this->paymentType = $paymentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentConditions(): ?string
    {
        return $this->paymentConditions;
    }

    /**
     * @param string $paymentConditions
     * @return CFDI
     */
    public function setPaymentConditions(?string $paymentConditions): self
    {
        $this->paymentConditions = $paymentConditions;
        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    /**
     * @param string $postalCode
     * @return CFDI
     */
    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getSignature(): ?string
    {
        return $this->signature;
    }

    /**
     * @param string $signature
     * @return CFDI
     */
    public function setSignature(?string $signature): self
    {
        $this->signature = $signature;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertificateNumber(): ?string
    {
        return $this->certificateNumber;
    }

    /**
     * @param string $certificateNumber
     * @return CFDI
     */
    public function setCertificateNumber(?string $certificateNumber): self
    {
        $this->certificateNumber = $certificateNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertificate(): ?string
    {
        return $this->certificate;
    }

    /**
     * @param string $certificate
     * @return CFDI
     */
    public function setCertificate(?string $certificate): self
    {
        $this->certificate = $certificate;
        return $this;
    }

    /**
     * @return string
     */
    public function getConfirmation(): ?string
    {
        return $this->confirmation;
    }

    /**
     * @param string $confirmation
     * @return CFDI
     */
    public function setConfirmation(?string $confirmation): self
    {
        $this->confirmation = $confirmation;
        return $this;
    }


    #########################
    ## CHILDREN
    #########################

    /**
     * @return Issuer
     */
    public function getIssuer(): ?Issuer
    {
        return $this->issuer;
    }

    /**
     * @param Issuer $issuer
     * @return CFDI
     */
    public function setIssuer(Issuer $issuer): self
    {
        $this->issuer = $issuer;
        return $this;
    }

    /**
     * @return Recipient
     */
    public function getRecipient(): ?Recipient
    {
        return $this->recipient;
    }

    /**
     * @param Recipient $recipient
     * @return CFDI
     */
    public function setRecipient(Recipient $recipient): self
    {
        $this->recipient = $recipient;
        return $this;
    }

    /**
     * @return ItemList
     */
    public function getItemList(): ?ItemList
    {
        return $this->itemList;
    }

    /**
     * @param ItemList $itemList
     * @return CFDI
     */
    public function setItemList(ItemList $itemList): self
    {
        $this->itemList = $itemList;
        return $this;
    }

    /**
     * @return RelatedCFDIList|null
     */
    public function getRelatedCFDIList(): ?RelatedCFDIList
    {
        return $this->relatedCFDIList;
    }

    /**
     * @param RelatedCFDIList|null $relatedCFDIList
     * @return CFDI
     */
    public function setRelatedCFDIList(?RelatedCFDIList $relatedCFDIList): self
    {
        $this->relatedCFDIList = $relatedCFDIList;
        return $this;
    }

    /**
     * @return Complement[]
     */
    public function getComplements(): ?array
    {
        return $this->complements;
    }

    /**
     * @param Complement $complement
     * @return CFDI
     */
    public function addComplement(Complement $complement): self
    {
        $this->complements[] = $complement;
        return $this;
    }

    /**
     * @param Complement[] $complements
     * @return CFDI
     */
    public function setComplements(array $complements): self
    {
        $this->complements = $complements;
        return $this;
    }

    /**
     * @return Taxes|null
     */
    public function getTaxes(): ?Taxes
    {
        return $this->taxes;
    }

    /**
     * @param Taxes|null $taxes
     * @return CFDI
     */
    public function setTaxes(?Taxes $taxes): self
    {
        $this->taxes = $taxes;
        return $this;
    }


    #########################
    ##      LIBRARY        ##
    #########################

    /**
     * Returns the filename of the trusted SAT Root CA used for validating certificates
     *
     * @return string|null
     */
    public static function SATRootCertificatePEM(): ?string
    {
        return PathUtility::join(__DIR__, '/../resources/certificates/sat-trusted-ca-prod.pem');
    }


    #########################
    ##       HELPER        ##
    #########################

    /**
     * Clean a string whitespace according to the CFDI Spec, used for generating an original chain sequence
     * @param string $s
     * @return string
     */
    public static function cleanWhitespace(string $s): string
    {
        // Replace all non visible characters with a single space
        $s = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $s);

        // Trim whitespace at the beginning and end of the string
        $s = trim($s);

        // Collapse multiple spaces into a single space
        $s = preg_replace('/\s+/u', ' ', $s);

        return $s;
    }
}