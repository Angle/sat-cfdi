<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

class Invoice extends Node
{
    #########################
    ##        PRESETS      ##
    #########################

    // none
    const SERIES_WRONG_LENGTH_ERROR = 1;


    #########################
    ##      PROPERTIES     ##
    #########################

    const NAME = 'cfdi:Comprobante';


    /**
     * Display Label = Version
     * @var string
     */
    protected $version = CFDI::VERSION;

    /**
     * Display Label = Serie
     * MaxLength = 25
     * RegExp = ^[a-zA-Z0-0]$
     * @var string
     */
    protected $series;

    protected $folio;

    protected $date;

    protected $signature;

    protected $paymentMethod; // Forma de Pago

    protected $certificateNumber;

    protected $certificate;

    protected $paymentConditions;

    protected $subTotal;

    protected $discount;

    protected $currency;

    protected $exchangeRate;

    protected $total;

    protected $invoiceType;

    protected $paymentType; // Método de Pago

    protected $postalCode;

    protected $confirmation;

    #########################
    ##
    #########################

    public function __construct(array $attr)
    {
        parent::__construct(self::NAME, array_merge($this->baseAttributes(), $attr));
    }

    public function baseAttributes(): array
    {
        return [
            'xmlns:cfdi'            => 'http://www.sat.gob.mx/cfd/3',
            'xmlns:xsi'             => 'http://www.w3.org/2001/XMLSchema-instance',
            'xsi:schemaLocation'    => 'http://www.sat.gob.mx/cfd/3 http://www.sat.gob.mx/sitio_internet/cfd/3/cfdv33.xsd',
            'Version'               => $this->version,
        ];
    }


    #########################
    ## GETTERS AND SETTERS ##
    #########################

    /**
     * @return string|null
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * @param string $version
     * @return Invoice
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    /**
     * @return string
     */
    public function getSeries(): string
    {
        return $this->series;
    }

    /**
     * @param string $serie
     * @return Invoice
     * @throws CFDIException
     */
    public function setSeries(string $serie): self
    {
        // Validate Length
        $l = strlen($serie);

        if ($l < 1 || $l > 25) {
            throw new CFDIException("Series contains wrong length.", self::SERIES_WRONG_LENGTH_ERROR);
        }

        // Validate contents
        if (preg_match('/^[a-zA-Z0-0]$/', $serie)) {

        }



        $this->serie = $serie;
        return $this;
    }
}