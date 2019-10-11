<?php

namespace Angle\CFDI\Catalog;

use RuntimeException;

abstract class TaxType
{
    const ISR     = '001';
    const IVA     = '002';
    const IEPS    = '003';

    private static $map = [
        self::ISR => [
            'name' => 'ISR',
            'retention' => true,
            'transfer' => false,
            'level' => 'federal'
        ],
        self::IVA => [
            'name' => 'IVA',
            'retention' => true,
            'transfer' => true,
            'level' => 'federal'
        ],
        self::IEPS => [
            'name' => 'IEPS',
            'retention' => true,
            'transfer' => true,
            'level' => 'federal'
        ],
    ];

    public static function listForFormBuilder($lang='es'): array
    {
        $a = [];

        foreach (self::$map as $key => $props) {
            $a[$props['name']] = $key;
        }

        return $a;
    }

    public static function getName($id, $lang='es'): ?string
    {
        if (!self::exists($id)) {
            return null;
        }

        return self::$map[$id]['name'];
    }

    public static function exists($id): bool
    {
        return array_key_exists($id, self::$map);
    }
}