<?php

namespace Angle\CFDI;

use DateTime;
use DOMDocument;

use Angle\CFDI\Node\Complement\FiscalStamp;

interface CFDIComplementInterface
{
    public function getUnknownNodes(): array;
}