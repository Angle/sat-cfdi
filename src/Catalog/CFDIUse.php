<?php

namespace Angle\CFDI\Catalog;

use RuntimeException;

/**
 * c_UsoCFDI
 */
abstract class CFDIUse
{
    const PAYMENTS = 'CP01';
    const PAYROLL = 'CN01';

    const MERCHANDISE_ACQUISITION    = 'G01';
    const REFUND_DISCOUNT_OR_BONUS = 'G02';
    const GENERAL_EXPENSE   = 'G03';

    const CONSTRUCTIONS = 'I01';
    const FURNITURE_OFFICE_EQUIPMENT = 'I02';
    const TRANSPORTATION_EQUIPMENT = 'I03';
    const COMPUTER_EQUIPMENT_AND_ACCESSORIES = 'I04';
    const DIES_MOLDS_AND_TOOLING = 'I05';
    const TELEPHONE_COMMUNICATIONS = 'I06';
    const SATELLITE_COMMUNICATIONS = 'I07';
    const OTHER_MACHINERY_AND_EQUIPMENT = 'I08';

    const MEDICAL_DENTAL_AND_HOSPITAL_FEES = 'D01';
    const DISABILITY_MEDICAL_EXPENSES = 'D02';
    const FUNERAL_EXPENSES = 'D03';
    const DONATIONS = 'D04';
    const MORTGAGE_INTEREST_EFFECTIVELY_PAID = 'D05';
    const VOLUNTARY_CONTRIBUTIONS_TO_SAR = 'D06';
    const MEDICAL_INSURANCE_PREMIUM = 'D07';
    const MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE = 'D08';
    const PENSION_AND_SAVINGS_DEPOSITS = 'D09';
    const EDUCATION_FEES_AND_TUITION = 'D10';

    const PENDING_DEFINITION = 'P01'; // TODO: deprecated, no longar available in CFDI4

    const NO_FISCAL_EFFECTS = 'S01';


    private static $map = [
        self::PAYMENTS => [
            'name' => [
                'en' => 'Payments',
                'es' => 'Pagos',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::PAYROLL => [
            'name' => [
                'en' => 'Payroll',
                'es' => 'Nómina',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::MERCHANDISE_ACQUISITION => [
            'name' => [
                'en' => 'Merchandise acquisition',
                'es' => 'Adquisición de mercancías',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::REFUND_DISCOUNT_OR_BONUS => [
            'name' => [
                'en' => 'Refunds, discounts or bonus',
                'es' => 'Devoluciones, descuentos o bonificaciones',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::GENERAL_EXPENSE => [
            'name' => [
                'en' => 'General expense',
                'es' => 'Gastos en general',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],

        self::CONSTRUCTIONS => [
            'name' => [
                'en' => 'Constructions',
                'es' => 'Construcciones',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::FURNITURE_OFFICE_EQUIPMENT => [
            'name' => [
                'en' => 'Furniture and office equipment due to investments',
                'es' => 'Mobiliario y equipo de oficina por inversiones',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::TRANSPORTATION_EQUIPMENT => [
            'name' => [
                'en' => 'Transportation Equipment',
                'es' => 'Equipo de transporte',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::COMPUTER_EQUIPMENT_AND_ACCESSORIES => [
            'name' => [
                'en' => 'Computer equipment and accessories',
                'es' => 'Equipo de computo y accesorios',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::DIES_MOLDS_AND_TOOLING => [
            'name' => [
                'en' => 'Dies, molds and tooling',
                'es' => 'Dados, troqueles, moldes, matrices y herramental',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::TELEPHONE_COMMUNICATIONS => [
            'name' => [
                'en' => 'Telephone communications',
                'es' => 'Comunicaciones telefónicas',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::SATELLITE_COMMUNICATIONS => [
            'name' => [
                'en' => 'Satellite communications',
                'es' => 'Comunicaciones satelitales',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::OTHER_MACHINERY_AND_EQUIPMENT => [
            'name' => [
                'en' => 'Other machinery and equipment',
                'es' => 'Otra Maquinaria y equipo',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],

        self::MEDICAL_DENTAL_AND_HOSPITAL_FEES => [
            'name' => [
                'en' => 'Medical, dental and hospital fees',
                'es' => 'Honorarios médicos, dentales y gastos hospitalarios',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::DISABILITY_MEDICAL_EXPENSES => [
            'name' => [
                'en' => 'Medical disability expenses',
                'es' => 'Gastos médicos por incapacidad o discapacidad',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::FUNERAL_EXPENSES => [
            'name' => [
                'en' => 'Funeral expenses',
                'es' => 'Gastos funerales',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::DONATIONS => [
            'name' => [
                'en' => 'Donations',
                'es' => 'Donativos',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::MORTGAGE_INTEREST_EFFECTIVELY_PAID => [
            'name' => [
                'en' => 'Mortgage interests efectively paid',
                'es' => 'Intereses reales efectivamente pagados por créditos hipotecarios (casa habitación)',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::VOLUNTARY_CONTRIBUTIONS_TO_SAR => [
            'name' => [
                'en' => 'Voluntary contributions to SAR',
                'es' => 'Aportaciones voluntarias al SAR',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::MEDICAL_INSURANCE_PREMIUM => [
            'name' => [
                'en' => 'Medical insurance premium',
                'es' => 'Primas por seguros de gastos médicos',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE => [
            'name' => [
                'en' => 'Mandatory school transportation expense',
                'es' => 'Gastos de transportación escolar obligatoria',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::PENSION_AND_SAVINGS_DEPOSITS => [
            'name' => [
                'en' => 'Pension and savings deposits',
                'es' => 'Depósitos en cuentas para el ahorro, primas que tengan como base planes de pensiones.',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::EDUCATION_FEES_AND_TUITION => [
            'name' => [
                'en' => 'Education fees and tuition',
                'es' => 'Pagos por servicios educativos (colegiaturas)',
            ],
            'natural_person'    => true,
            'legal_entity'      => false,
        ],
        self::PENDING_DEFINITION => [
            'name' => [
                'en' => 'Pending definition',
                'es' => 'Por definir',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
        ],
        self::NO_FISCAL_EFFECTS => [
            'name' => [
                'en' => 'No fiscal effects',
                'es' => 'Sin efectos fiscales',
            ],
            'natural_person'    => true,
            'legal_entity'      => true,
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

    public static function isNaturalPersonValid($id): bool
    {
        if (!self::exists($id)) {
            return false;
        }

        return self::$map[$id]['natural_person'];
    }

    public static function isLegalEntityValid($id): bool
    {
        if (!self::exists($id)) {
            return false;
        }

        return self::$map[$id]['legal_entity'];
    }

    public static function exists($id): bool
    {
        return array_key_exists($id, self::$map);
    }
}