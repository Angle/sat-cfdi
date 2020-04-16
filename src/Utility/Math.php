<?php

namespace Angle\CFDI\Utility;

abstract class Math
{
    static $PRECISION = 6;

    /**
     * @param string|float $a left operand
     * @param string|float $b right operand
     * @return string|float
     */
    public static function add($a, $b)
    {
        if (extension_loaded('bcmath')) {
            return bcadd($a, $b, self::$PRECISION);
        } else {
            return $a + $b;
        }
    }

    /**
     * @param string|float $a left operand
     * @param string|float $b right operand
     * @return string|float
     */
    public static function sub($a, $b)
    {
        if (extension_loaded('bcmath')) {
            return bcsub($a, $b, self::$PRECISION);
        } else {
            return $a + $b;
        }
    }

    /**
     * @param string|float $a left operand
     * @param string|float $b right operand
     * @return string|float
     */
    public static function mul($a, $b)
    {
        if (extension_loaded('bcmath')) {
            return bcmul($a, $b, self::$PRECISION);
        } else {
            return $a * $b;
        }
    }

    /**
     * @param string|float $a left operand
     * @param string|float $b right operand
     * @return string|float
     */
    public static function div($a, $b)
    {
        if (extension_loaded('bcmath')) {
            return bcdiv($a, $b, self::$PRECISION);
        } else {
            return $a / $b;
        }
    }

    /**
     * @param string|float $a left operand
     * @param string|float $b right operand
     * @return bool
     */
    public static function equal($a, $b)
    {
        if (extension_loaded('bcmath')) {
            return (bccomp($a, $b, self::$PRECISION) == 0);
        } else {
            return (self::round($a, self::$PRECISION) == self::round($b, self::$PRECISION));
        }
    }

    /**
     * Returns a rounded string, without thousands separators, with the precision specified
     * @param string|float $a number
     * @param int $precision
     * @return string
     */
    public static function round($a, $precision=0)
    {
        if ($precision < 0) throw new \RuntimeException('Negative precision values are not supported');

        if (extension_loaded('bcmath')) {
            return self::bcround($a, $precision);
        } else {
            return number_format($a, $precision, '.', '');
        }
    }


    private static function bcceil(string $number)
    {
        if (strpos($number, '.') !== false) {
            if (preg_match("~\.[0]+$~", $number)) return bcround($number, 0);
            if ($number[0] != '-') return bcadd($number, 1, 0);
            return bcsub($number, 0, 0);
        }
        return $number;
    }

    private static function bcfloor(string $number)
    {
        if (strpos($number, '.') !== false) {
            if (preg_match("~\.[0]+$~", $number)) return bcround($number, 0);
            if ($number[0] != '-') return bcadd($number, 0, 0);
            return bcsub($number, 1, 0);
        }
        return $number;
    }

    private static function bcround(string $number, $precision = 0)
    {
        if (strpos($number, '.') !== false) {
            if ($number[0] != '-') return bcadd($number, '0.' . str_repeat('0', $precision) . '5', $precision);
            return bcsub($number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }
        return $number;
    }
}