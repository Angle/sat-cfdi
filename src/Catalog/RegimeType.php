<?php

namespace Angle\CFDI\Catalog;

abstract class RegimeType
{
    const GENERAL_DE_LEY_PERSONAS_MORALES                           = '601';
    const PERSONAS_MORALES_CON_FINES_NO_LUCRATIVOS                  = '603';
    const SUELDOS_SALARIOS_Y_ASIMILADOS                             = '605';
    const ARRENDAMIENTO                                             = '606';
    const DEMAS_INGRESOS                                            = '608';
    const CONSOLIDACION                                             = '609';
    const RESIDENTES_EN_EL_EXTRANJERO                               = '610';
    const INGRESOS_POR_DIVIDENDOS                                   = "611";
    const PERSONAS_FISICAS_CON_ACTIVIDAD_EMPRESARIAL                = "612";
    const INGRESOS_POR_INTERESES                                    = "614";
    const SIN_OBLIGACIONES_FISCALES                                 = '616';
    const SOCIEDADES_COOPERATIVAS_DE_PRODUCCION_INGRESOS_DIFERIDOS  = '620';
    const INCORPORACION_FISCAL                                      = '621';
    const ACTIVIDADES_AGRICOLAS_GANADERAS_SILVICOLAS_PESQUERAS      = '622';
    const OPCIONAL_PARA_GRUPO_DE_SOCIEDADES                         = '623';
    const COORDINADOS                                               = '624';
    const HIDROCARBUROS                                             = '628';
    const ENAJENACION_O_ADQUISICION_DE_BIENES                       = '607';
    const PREFERENTES_Y_EMPRESAS_MULTINACIONALES                    = '629';
    const ENAJENACION_DE_ACCIONES_EN_BOLSA_DE_VALORES               = '630';
    const INGRESOS_POR_OBTENCION_DE_PREMIOS                         = '615';

    private static $map = [
        self::GENERAL_DE_LEY_PERSONAS_MORALES => [
            'name'              => 'General de Ley Personas Morales',
            'natural_person'    => false,
            'legal_entity'      => true,
        ],
        // TODO: write all of them...
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