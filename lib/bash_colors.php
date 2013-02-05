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

/**
 * Generates a string with ANSI format codes
 * @param string $str
 * @param string color
 * @param bool $bold
 */
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

/**
 * Gets a list of strings and return a escaped string to output
 * @private
 * @param string message...
 * @return string
 */
function cleanupStrings($args) {
    $str = array();
    foreach($args as $part) {
        $str[] = trim($part);
    }
    
    // just in case of a line with double quotes
    $str = implode(' ', $str);
    $str = trim($str);
    return $str;
}

/**
 * Send output to pipe or stdout
 * @private
 */
function output($str) {
    if (OUTPUT_IS_TTY) {
        fwrite(\STDOUT, $str);
    } else {
        echo stripslashes($str);
    }
}

/**
 * Writes output
 * @param string $str...
 */
function write() {
    $str = cleanupStrings(func_get_args());
    output($str);
}

/**
 * Writes output and starts a new line
 * @param string $str...
 */
function writeln() {
    $str = cleanupStrings(func_get_args()) . "\n";
    output($str);
}
