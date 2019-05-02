<?php

namespace Angle\CFDI\Invoice;

use Angle\CFDI\CFDI;
use Angle\CFDI\CFDIException;

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

}