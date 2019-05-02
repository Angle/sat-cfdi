<?php

namespace Angle\CFDI\Node;

use Angle\CFDI\CFDIException;
use DOMElement;

abstract class Node
{
    protected $name;

    /**
     * @var DOMElement
     */
    protected $element;

    public function __construct($name, array $attr)
    {
        $this->element = new DOMElement($name);
        $this->setAttributes($attr);

    }

    public function setAttributes(array $attr)
    {
        if (!$this->element || !($this->element instanceof DOMElement)) {
            throw new CFDIException('Cannot set Node Attributes for an element that does not yet exist');
        }

        foreach ($attr as $key => $value) {
            $this->element->setAttribute($key, $value);
        }
    }

}