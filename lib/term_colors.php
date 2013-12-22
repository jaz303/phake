<?php
/**
 * Adds some message coloring to bash
 * Usage inside tasks:
 *
 *      write( red('star'), green('leaf'), blue('sky'), yellow('stone'), 'or', bold('bolded text') );
 *
 * @see http://en.wikipedia.org/wiki/ANSI_escape_code#Colors
 */

/**
 * Generates a string with ANSI format codes
 * @param string $str
 * @param string color
 * @param bool $bold
 */
function colorize($str, $color, $bold = false) {
    static $colors = array(
        'black' => '30',
        'blue' => '34',
        'green' => '32',
        'cyan' => '36',
        'red' => '31',
        'purple' => '35',
        'yellow' => '33',
        'white' => '37'
    );

    $code = $colors[$color];
    $bold = (int)$bold;
    $bg = $color == 'black' ? "\033[47m" : '';

    return \phake\Utils::is_tty() ?
        "\033[{$bold};{$code}m{$bg}{$str}\033[0m":
        $str;
}

function bold($str) {
    return \phake\Utils::is_tty() ?
        "\033[1m{$str}\033[2m" :
        $str;
}

function white($str, $bold = false) {
    return colorize($str, 'white', $bold);
}

function red($str, $bold = false) {
    return colorize($str, 'red', $bold);
}

function green($str, $bold = false) {
    return colorize($str, 'green', $bold);
}

function blue($str, $bold = false) {
    return colorize($str, 'blue', $bold);
}

function yellow($str, $bold = false) {
    return colorize($str, 'yellow', $bold);
}

function cyan($str, $bold = false) {
    return colorize($str, 'cyan', $bold);
}

function purple($str, $bold = false) {
    return colorize($str, 'purple', $bold);
}

function black($str, $bold = false) {
    return colorize($str, 'black', $bold);
}
