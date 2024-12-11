<?php

namespace Angle\CFDI\Catalog;

use RuntimeException;

abstract class TaxFactorType
{
    const RATE      = 'Tasa';
    const FEE       = 'Cuota';
    const EXEMPT    = 'Exento';

    private static $map = [
        self::RATE => [
            'name' => [
                'en' => 'Rate',
                'es' => 'Tasa',
            ],
        ],
        self::FEE => [
            'name' => [
                'en' => 'Fee',
                'es' => 'Cuota',
            ],
        ],
        self::EXEMPT => [
            'name' => [
                'en' => 'Exempt',
                'es' => 'Exento',
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

            $label = $key . ' - ' . $props['name'][$lang];
            $a[$label] = $key;
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