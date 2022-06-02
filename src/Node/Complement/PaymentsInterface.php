<?php

namespace Angle\CFDI\Node\Complement;

interface PaymentsInterface
{
    /**
     * @return PaymentInterface[]|null
     */
    public function getPayments(): ?array;
}