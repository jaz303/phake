<?php
/**
 * Adds some message coloring to bash
 * Usage inside tasks:
 * 
 *      write( red('star'), green('leaf'), blue('sky'), yellow('stone'), 'or', bold('bolded text') );
 * 
 */
define('TPUT_RESET', '$(tput sgr0)');

function color($str, $color) {
    return '$(tput setaf '.$color.')' . $str . TPUT_RESET;
}

function bold($str) {
    return '$(tput bold)' . $str . TPUT_RESET;
}

function red($str) {
    return color($str, 1);
}

function green($str) {
    return color($str, 2);
}

function blue($str) {
    return color($str, 4);
}

function yellow($str) {
    return color($str, 3);
}

function write() {
    $str = array();
    foreach(func_get_args() as $part) {
        $str[] = trim($part);
    }
    // just in case of a line with double quotes
    $str = implode(' ', $str);
    $line = trim(addcslashes($str, '"'));
    echo `echo "$line"`;
}
