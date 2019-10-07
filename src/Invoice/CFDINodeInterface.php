<?php

namespace Angle\CFDI\Invoice;

use DOMDocument;
use DOMElement;
use DOMNode;

interface CFDINodeInterface
{
    public function validate(): bool;

    public function getAttributes(): array;

    public function setChildren(array $children): void;

    public function toDOMElement(DOMDocument $dom): DOMElement;
}