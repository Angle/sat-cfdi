<?php

namespace Angle\CFDI\Catalog;

use RuntimeException;

/**
 * c_UsoCFDI
 */
abstract class CFDIUse
{
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
    const DISABILITY_MEDIAL_EXPENSES = 'D02';
    const FUNERAL_EXPENSES = 'D03';
    const DONATIONS = 'D04';
    const MORTGAGE_INTEREST_EFFECTIVELY_PAID = 'D05';
    const VOLUNTARY_CONTRIBUTIONS_TO_SAR = 'D06';
    const MEDICAL_INSURANCE_PREMIUM = 'D07';
    const MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE = 'D08';
    const PENSION_AND_SAVINGS_DEPOSITS = 'D09';
    const EDUCATION_FEES_AND_TUITION = 'D10';

    const PENDING_DEFINITION = 'P01';


    private static $map = [
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


        // TODO: Implement the others


        self::PENDING_DEFINITION => [
            'name' => [
                'en' => 'Pending definition',
                'es' => 'Por definir',
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

    public static function exists($id): bool
    {
        return array_key_exists($id, self::$map);
    }
}