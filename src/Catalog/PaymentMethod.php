<?php

namespace Angle\CFDI\Catalog;

use RuntimeException;

abstract class PaymentMethod
{
    const CASH        = '01';
    const CHECK   = '02';

    // TODO: finish this catalog

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