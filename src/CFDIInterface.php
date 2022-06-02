<?php

namespace Angle\CFDI;

use DateTime;
use DOMDocument;
use DOMNode;

use Angle\CFDI\Node\Complement\FiscalStamp;
use Angle\CFDI\Node\Complement\PaymentsInterface;

interface CFDIInterface
{
    public function getVersion(): ?string;

    public function getUuid(): ?string;

    public function getIssuerRfc(): ?string;

    public function getRecipientRfc(): ?string;

    public function getTotalAmount(): ?string;

    public function getDate(): ?DateTime;

    public function getCertificate(): ?string;

    public function getCertificateNumber(): ?string;

    public function getSignature(): ?string;

    // Fiscal Stamp
    public function getFiscalStamp(): ?FiscalStamp; // TODO: this should point to a CFDIFiscalStampInterface

    // Payment Complement
    public function getPaymentComplement(): ?PaymentsInterface;

    // Complements
    /** @return CFDIComplementInterface[]|null */
    public function getComplements(): ?array;

    // XML Functions
    public static function createFromDOMNode(DOMNode $node);

    public function getOriginalXml(): ?string;

    public function setOriginalXml(?string $xmlString);

    public function toDOMDocument(): DOMDocument;

    public function toXML();
}