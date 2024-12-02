<?php

namespace Angle\CFDI\Catalog;

abstract class RegimeType
{
    const GENERAL_DE_LEY_PERSONAS_MORALES                           = '601';
    const PERSONAS_MORALES_CON_FINES_NO_LUCRATIVOS                  = '603';
    const SUELDOS_SALARIOS_Y_ASIMILADOS                             = '605';
    const ARRENDAMIENTO                                             = '606';
    const ENAJENACION_O_ADQUISICION_DE_BIENES                       = '607';
    const DEMAS_INGRESOS                                            = '608';
    const RESIDENTES_EN_EL_EXTRANJERO                               = '610';
    const INGRESOS_POR_DIVIDENDOS                                   = '611';
    const PERSONAS_FISICAS_CON_ACTIVIDAD_EMPRESARIAL                = '612';
    const INGRESOS_POR_INTERESES                                    = '614';
    const INGRESOS_POR_OBTENCION_DE_PREMIOS                         = '615';
    const SIN_OBLIGACIONES_FISCALES                                 = '616';
    const SOCIEDADES_COOPERATIVAS_DE_PRODUCCION_INGRESOS_DIFERIDOS  = '620';
    const INCORPORACION_FISCAL                                      = '621';
    const ACTIVIDADES_AGRICOLAS_GANADERAS_SILVICOLAS_PESQUERAS      = '622';
    const OPCIONAL_PARA_GRUPO_DE_SOCIEDADES                         = '623';
    const COORDINADOS                                               = '624';
    const ACTIVIDADES_EMPRESARIALES_EN_PLATAFORMAS_TECNOLOGICAS     = '625';
    const SIMPLIFICADO_DE_CONFIANZA                                 = '626';


    private static $map = [
        self::GENERAL_DE_LEY_PERSONAS_MORALES => [
            'name'              => 'General de Ley Personas Morales',
            'natural_person'    => false,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::PERSONAS_MORALES_CON_FINES_NO_LUCRATIVOS => [
            'name'              => 'Personas Morales con Fines no Lucrativos',
            'natural_person'    => false,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::SUELDOS_SALARIOS_Y_ASIMILADOS => [
            'name'              => 'Sueldos y Salarios e Ingresos Asimilados a Salarios',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::PAYMENTS,
                CFDIUse::PAYROLL,
            ],
        ],
        self::ARRENDAMIENTO => [
            'name'              => 'Arrendamiento',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::ENAJENACION_O_ADQUISICION_DE_BIENES => [
            'name'              => 'Régimen de Enajenación o Adquisición de Bienes',
            'natural_person'    => false,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::DEMAS_INGRESOS => [
            'name'              => 'Demás ingresos',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::RESIDENTES_EN_EL_EXTRANJERO => [
            'name'              => 'Residentes en el Extranjero sin Establecimiento Permanente en México',
            'natural_person'    => true,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::INGRESOS_POR_DIVIDENDOS => [
            'name'              => 'Ingresos por Dividendos (socios y accionistas)',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::PERSONAS_FISICAS_CON_ACTIVIDAD_EMPRESARIAL => [
            'name'              => 'Personas Físicas con Actividades Empresariales y Profesionales',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::INGRESOS_POR_INTERESES => [
            'name'              => 'Ingresos por intereses',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::INGRESOS_POR_OBTENCION_DE_PREMIOS => [
            'name'              => 'Régimen de los ingresos por obtención de premios',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::PAYMENTS,
                CFDIUse::PAYROLL,
            ],
        ],
        self::SIN_OBLIGACIONES_FISCALES => [
            'name'              => 'Sin obligaciones fiscales',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::SOCIEDADES_COOPERATIVAS_DE_PRODUCCION_INGRESOS_DIFERIDOS => [
            'name'              => 'Sociedades Cooperativas de Producción que optan por diferir sus ingresos',
            'natural_person'    => false,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::INCORPORACION_FISCAL => [
            'name'              => 'Incorporación Fiscal',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::ACTIVIDADES_AGRICOLAS_GANADERAS_SILVICOLAS_PESQUERAS => [
            'name'              => 'Actividades Agrícolas, Ganaderas, Silvícolas y Pesqueras',
            'natural_person'    => true,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::OPCIONAL_PARA_GRUPO_DE_SOCIEDADES => [
            'name'              => 'Opcional para Grupos de Sociedades',
            'natural_person'    => false,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::COORDINADOS => [
            'name'              => 'Coordinados',
            'natural_person'    => false,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::ACTIVIDADES_EMPRESARIALES_EN_PLATAFORMAS_TECNOLOGICAS => [
            'name'              => 'Hidrocarburos',
            'natural_person'    => false,
            'legal_entity'      => true,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::MEDICAL_DENTAL_AND_HOSPITAL_FEES,
                CFDIUse::DISABILITY_MEDICAL_EXPENSES,
                CFDIUse::FUNERAL_EXPENSES,
                CFDIUse::DONATIONS,
                CFDIUse::MORTGAGE_INTEREST_EFFECTIVELY_PAID,
                CFDIUse::VOLUNTARY_CONTRIBUTIONS_TO_SAR,
                CFDIUse::MEDICAL_INSURANCE_PREMIUM,
                CFDIUse::MANDATORY_SCHOOL_TRANSPORTATION_EXPENSE,
                CFDIUse::PENSION_AND_SAVINGS_DEPOSITS,
                CFDIUse::EDUCATION_FEES_AND_TUITION,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
        ],
        self::SIMPLIFICADO_DE_CONFIANZA => [
            'name'              => 'De los Regímenes Fiscales Preferentes y de las Empresas Multinacionales',
            'natural_person'    => true,
            'legal_entity'      => false,
            'cfdi_use'          => [
                CFDIUse::MERCHANDISE_ACQUISITION,
                CFDIUse::REFUND_DISCOUNT_OR_BONUS,
                CFDIUse::GENERAL_EXPENSE,
                CFDIUse::CONSTRUCTIONS,
                CFDIUse::FURNITURE_OFFICE_EQUIPMENT,
                CFDIUse::TRANSPORTATION_EQUIPMENT,
                CFDIUse::COMPUTER_EQUIPMENT_AND_ACCESSORIES,
                CFDIUse::DIES_MOLDS_AND_TOOLING,
                CFDIUse::TELEPHONE_COMMUNICATIONS,
                CFDIUse::SATELLITE_COMMUNICATIONS,
                CFDIUse::OTHER_MACHINERY_AND_EQUIPMENT,
                CFDIUse::NO_FISCAL_EFFECTS,
                CFDIUse::PAYMENTS,
            ],
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

    public static function listForFormBuilderNaturalPerson($lang='es'): array
    {
        $a = [];

        foreach (self::$map as $key => $props) {
            if ($props['natural_person']) {
                $a[$props['name']] = $key;
            }
        }

        return $a;
    }

    public static function listForFormBuilderLegalEntity($lang='es'): array
    {
        $a = [];

        foreach (self::$map as $key => $props) {
            if ($props['legal_entity']) {
                $a[$props['name']] = $key;
            }
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

    public static function getValidCfdiUse($id): ?array
    {
        if (!self::exists($id)) {
            return false;
        }
        return self::$map[$id]['cfdi_use'];
    }

    public static function exists($id): bool
    {
        return array_key_exists($id, self::$map);
    }
}