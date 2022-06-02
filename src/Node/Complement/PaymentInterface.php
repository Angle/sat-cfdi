<?php

namespace Angle\CFDI\Node\Complement;

interface PaymentInterface
{
    public function getCurrency(): ?string;

    public function getAmount(): ?string;

    public function getPaymentMethod(): ?string;

    public function getExchangeRate(): ?string;
}