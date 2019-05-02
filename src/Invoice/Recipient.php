<?php

namespace Angle\CFDI\Invoice;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

use DOMDocument;
use DOMElement;

class Recipient
{
    const NODE_NAME = "cfdi:Receptor";

    /**
     * @var string
     */
    protected $rfc;

    /**
     * @var string
     */
    protected $name;



    public function toDOMElement(DOMDocument $dom): DOMElement
    {

    }

}