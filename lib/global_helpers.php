<?php
function builder() {
    return \Phake\Builder::$global;
}

function task() {
    $deps = func_get_args();
    $name = array_shift($deps);
    if ($deps[count($deps) - 1] instanceof Closure) {
        $work = array_pop($deps);
    } else {
        $work = null;
    }
    builder()->add_task($name, $work, $deps);
}

function group($name, $lambda = null) {
    builder()->push_group($name);
    if ($lambda instanceof Closure) $lambda();
    builder()->pop_group();
}

function before($task, $lambda) {
    builder()->before($task, $lambda);
}

function after($task, $lambda) {
    builder()->after($task, $lambda);
}

function desc($description) {
    builder()->desc($description);
}

/**
 * Writes arguments to stdout, each separated by a space
 * @param string $str...
 */
function write() {
    $out = '';
    $was_newline = true;
    foreach (func_get_args() as $ix => $str) {
        if (!$was_newline) {
            $out .= ' ';
        }
        $out .= $str;
        $was_newline = $str[strlen($str)-1] == "\n";
    }
    fwrite(STDOUT, $out);
}

/**
 * Writes arguments to stdout, each separated by a space, then starts a new line
 * @param string $str...
 */
function writeln() {
    call_user_func_array('write', func_get_args());
    fwrite(STDOUT, "\n");
}

require_once 'term_colors.php';
