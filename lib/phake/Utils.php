<?php

namespace phake;

class Utils
{
    public static function parse_args(array $args) {
        $out = array();
        $pos = 0;
        foreach ($args as $arg) {
            list($k, $v) = explode('=', $arg);
            if (!isset($v)) {
                $out[$pos++] = $k;
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    private static $is_tty = null;

    public static function is_tty() {
        if (self::$is_tty === null) {
            self::$is_tty = function_exists('posix_isatty') ? posix_isatty(STDOUT) : false;
        }
        return self::$is_tty;
    }
}
