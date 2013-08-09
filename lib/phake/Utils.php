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
}
