<?php

namespace Angle\CFDI\Utility;

abstract class PathUtility
{
    public static function join() {
        $paths = array();

        foreach (func_get_args() as $arg) {
            if ($arg !== '') { $paths[] = $arg; }
        }

        return preg_replace('#/+#','/',join('/', $paths));
    }
}