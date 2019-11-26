<?php

namespace Angle\CFDI\Catalog;

use RuntimeException;

abstract class PaymentMethod
{
    const CASH                          = '01';
    const CHECK                         = '02';
    const TRANSFER                      = '03';
    const CREDIT_CARD                   = '04';
    const ELECTRONIC_WALLET             = '05';
    const ELECTRONIC_MONEY              = '06';
    const GROCERY_VOUCHERS              = '08';
    const DATION_IN_PAYMENT             = '12'; // Dación en Pago
    const SUBROGATION_PAYMENT           = '13';
    const CONSIGNMENT_PAYMENT           = '14';
    const CONDONE                       = '15'; // Condonación
    const COMPENSATION                  = '17';
    const NOVATION                      = '23';
    const CONFUSION                     = '24';
    const DEBT_REMISSION                = '25';
    const PRESCRIPTION_OR_EXPIRATION    = '26';
    const CREDITORS_SATISFACTION        = '27';
    const DEBIT_CARD                    = '28';
    const SERVICE_CARD                  = '29';
    const ADVANCE_APPLICATION           = '30';
    const PAYMENT_INTERMEDIARY          = '31';
    const TO_BE_DEFINED                 = '99';


    // TODO: Finish this catalog

    /*
    private static $map = [
        self::INCOME => [
            'name' => [
                'en' => 'Income',
                'es' => 'Ingreso',
            ],
        ],
        self::EXPENDITURE => [
            'name' => [
                'en' => 'Expenditure',
                'es' => 'Egreso',
            ],
        ],
        self::TRANSFER => [
            'name' => [
                'en' => 'Transfer',
                'es' => 'Traslado',
            ],
        ],
        self::PAYSLIP => [
            'name' => [
                'en' => 'Payslip',
                'es' => 'Nómina',
            ],
        ],
        self::PAYMENT => [
            'name' => [
                'en' => 'Payment',
                'es' => 'Pago',
            ],
        ],
    ];
    */

    public static function listForFormBuilder($lang='es'): array
    {
        $a = [];

        foreach (self::$map as $key => $props) {
            if (!array_key_exists($lang, $props['name'])) {
                throw new RuntimeException(sprintf('Language \'%s\' is not registered', $lang));
            }

            $a[$props['name'][$lang]] = $key;
        }

        return $a;
    }

    public static function getName($id, $lang='es'): ?string
    {
        if (!self::exists($id)) {
            return null;
        }

        if (!array_key_exists($lang, self::$map[$id]['name'])) {
            throw new RuntimeException(sprintf('Language \'%s\' is not registered', $lang));
        }

        return self::$map[$id]['name'][$lang];
    }

    public static function exists($id): bool
    {
        return array_key_exists($id, self::$map);
    }
}