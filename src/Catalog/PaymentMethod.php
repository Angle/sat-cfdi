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

    private static $map = [
        self::CASH => [
            'name' => [
                'en' => 'Cash',
                'es' => 'Efectivo',
            ],
        ],
        self::CHECK => [
            'name' => [
                'en' => 'Check',
                'es' => 'Cheque nominativo',
            ],
        ],
        self::TRANSFER => [
            'name' => [
                'en' => 'Wire Transfer',
                'es' => 'Transferencia electrónica de fondos',
            ],
        ],
        self::CREDIT_CARD => [
            'name' => [
                'en' => 'Credit Card',
                'es' => 'Tarjeta de crédito',
            ],
        ],
        self::ELECTRONIC_WALLET => [
            'name' => [
                'en' => 'Electronic Wallet',
                'es' => 'Monedero electrónico',
            ],
        ],
        self::ELECTRONIC_MONEY => [
            'name' => [
                'en' => 'Electronic Money',
                'es' => 'Dinero electrónico',
            ],
        ],
        self::GROCERY_VOUCHERS => [
            'name' => [
                'en' => 'Grocery Vouchers',
                'es' => 'Vales de despensa',
            ],
        ],
        self::DATION_IN_PAYMENT => [
            'name' => [
                'en' => 'Dation in Payment',
                'es' => 'Dación en pago',
            ],
        ],
        self::SUBROGATION_PAYMENT => [
            'name' => [
                'en' => 'Subrogation Payment',
                'es' => 'Pago por subrogación',
            ],
        ],
        self::CONSIGNMENT_PAYMENT => [
            'name' => [
                'en' => 'Consignment Payment',
                'es' => 'Pago por consignación',
            ],
        ],
        self::CONDONE => [
            'name' => [
                'en' => 'Condone',
                'es' => 'Condonación',
            ],
        ],
        self::COMPENSATION => [
            'name' => [
                'en' => 'Compensation',
                'es' => 'Compensación',
            ],
        ],
        self::NOVATION => [
            'name' => [
                'en' => 'Novation',
                'es' => 'Novación',
            ],
        ],
        self::CONFUSION => [
            'name' => [
                'en' => 'Confusion',
                'es' => 'Confusión',
            ],
        ],
        self::DEBT_REMISSION => [
            'name' => [
                'en' => 'Debt Remission',
                'es' => 'Remisión de deuda',
            ],
        ],
        self::PRESCRIPTION_OR_EXPIRATION => [
            'name' => [
                'en' => 'Prescription or Expiration',
                'es' => 'Prescripción o caducidad',
            ],
        ],
        self::CREDITORS_SATISFACTION => [
            'name' => [
                'en' => 'Creditors Satisfaction',
                'es' => 'A satisfacción del acreedor',
            ],
        ],
        self::DEBIT_CARD => [
            'name' => [
                'en' => 'Debit Card',
                'es' => 'Tarjeta de débito',
            ],
        ],
        self::SERVICE_CARD => [
            'name' => [
                'en' => 'Service Card',
                'es' => 'Tarjeta de servicios',
            ],
        ],
        self::ADVANCE_APPLICATION => [
            'name' => [
                'en' => 'Advance Application',
                'es' => 'Aplicación de anticipos',
            ],
        ],
        self::PAYMENT_INTERMEDIARY => [
            'name' => [
                'en' => 'Payment Intermediary',
                'es' => 'Intermediario pagos',
            ],
        ],
        self::TO_BE_DEFINED => [
            'name' => [
                'en' => 'To be defined',
                'es' => 'Por definir',
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