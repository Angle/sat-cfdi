<?php

namespace Angle\CFDI\Invoice;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use DOMElement;

class Issuer
{

    const NODE_NAME = "cfdi:Emisor";

    /**
     * @var string
     */
    protected $rfc;

    /**
     * @var string
     */
    protected $name;


    public function toDOMElement(): DOMElement
    {

    }

}