<?php
/**
 * Adds some message coloring to bash
 * Usage inside tasks:
 * 
 *      write( red('star'), green('leaf'), blue('sky'), yellow('stone'), 'or', bold('bolded text') );
 *
 * @see http://en.wikipedia.org/wiki/ANSI_escape_code#Colors
 */

$_COLORS = array(
    'black' => '30',
    'blue' => '34',
    'green' => '32',
    'cyan' => '36',
    'red' => '31',
    'purple' => '35',
    'yellow' => '33',
    'white' => '37'
);

// Const for TTY detection result
define('OUTPUT_IS_TTY', posix_isatty(STDOUT));

function color($str, $color, $bold = false) {
    global $_COLORS;
    $code = $_COLORS[$color];
    $bold = (int)$bold;
    // black color will output with a background for readability. Is this OK?
    $bg = $color == 'black' ? "\033[47m" : '';
    return OUTPUT_IS_TTY ?
        "\033[{$bold};{$code}m{$bg}{$str}\033[0m":
        $str;
}

function bold($str) {
    return OUTPUT_IS_TTY ?
        "\033[1m{$str}\033[2m" :
        $str;
}

function white($str, $bold = false) {
    return color($str, 'white', $bold);
}

function red($str, $bold = false) {
    return color($str, 'red', $bold);
}

function green($str, $bold = false) {
    return color($str, 'green', $bold);
}

function blue($str, $bold = false) {
    return color($str, 'blue', $bold);
}

function yellow($str, $bold = false) {
    return color($str, 'yellow', $bold);
}

function cyan($str, $bold = false) {
    return color($str, 'cyan', $bold);
}

function purple($str, $bold = false) {
    return color($str, 'purple', $bold);
}

function black($str, $bold = false) {
    return color($str, 'black', $bold);
}

function write() {
    $str = array();
    foreach(func_get_args() as $part) {
        $str[] = trim($part);
    }
    // just in case of a line with double quotes
    $str = implode(' ', $str);
    $str = trim(addcslashes($str, '"'));
    if (OUTPUT_IS_TTY) {
        echo `echo "$str"`;
    } else {
        echo "$str\n";
    }
}
