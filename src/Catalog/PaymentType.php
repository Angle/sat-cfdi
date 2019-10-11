<?php

namespace Angle\CFDI\Catalog;

use RuntimeException;

/**
 * c_MetodoPago
 *
 * Class PaymentType
 * @package Angle\CFDI\Catalog
 */
abstract class PaymentType
{
    const SINGLE    = 'PUE';
    const PARTIAL   = 'PPD';


    private static $map = [
        self::SINGLE => [
            'name' => [
                'en' => 'One single payment',
                'es' => 'Pago en una sola exhibición',
            ],
        ],
        self::PARTIAL => [
            'name' => [
                'en' => 'Partial or deferred payment',
                'es' => 'Pago en parcialidades o diferido',
            ],
        ],
    ];

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