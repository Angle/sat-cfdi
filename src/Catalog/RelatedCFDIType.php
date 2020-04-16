<?php

namespace Angle\CFDI\Catalog;

use RuntimeException;

/**
 * Reference: catCFDI:c_TipoRelacion
 */
abstract class RelatedCFDIType
{
    const CREDIT_NOTE                   = '01';
    const DEBIT_NOTE                    = '02';
    const RETURN_OF_GOODS               = '03';
    const SUBSTITUTION                  = '04';
    const SHIPMENT_OF_GOODS             = '05';
    const PREVIOUS_SHIPMENT_OF_GOODS    = '06';
    const ADVANCE_PAYMENT_APPLICATION   = '07';
    const INVOICE_PARTIAL_PAYMENT       = '08';
    const INVOICE_DEFERRED_PAYMENT      = '09';

    private static $map = [
        self::CREDIT_NOTE => [
            'name' => [
                'en' => '',
                'es' => 'Nota de crédito de los documentos relacionados',
            ],
        ],
        self::DEBIT_NOTE => [
            'name' => [
                'en' => '',
                'es' => 'Nota de débito de los documentos relacionados',
            ],
        ],
        self::RETURN_OF_GOODS => [
            'name' => [
                'en' => '',
                'es' => 'Devolución de mercancía sobre facturas o traslados previos',
            ],
        ],
        self::SUBSTITUTION => [
            'name' => [
                'en' => '',
                'es' => 'Sustitución de los CFDI previos',
            ],
        ],
        self::SHIPMENT_OF_GOODS => [
            'name' => [
                'en' => '',
                'es' => 'Traslados de mercancias facturados previamente',
            ],
        ],
        self::PREVIOUS_SHIPMENT_OF_GOODS => [
            'name' => [
                'en' => '',
                'es' => 'Factura generada por los traslados previos',
            ],
        ],
        self::ADVANCE_PAYMENT_APPLICATION => [
            'name' => [
                'en' => '',
                'es' => 'CFDI por aplicación de anticipo',
            ],
        ],
        self::INVOICE_PARTIAL_PAYMENT => [
            'name' => [
                'en' => '',
                'es' => 'Factura generada por pagos en parcialidades',
            ],
        ],
        self::INVOICE_DEFERRED_PAYMENT => [
            'name' => [
                'en' => '',
                'es' => 'Factura generada por pagos diferidos',
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