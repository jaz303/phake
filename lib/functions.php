<?php
function builder() {
    if (!isset(phake\Builder::$global)) {
        phake\Builder::$global = new phake\Builder;
    }
    return phake\Builder::$global;
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
    $thrown = null;
    builder()->push_group($name);
    try {
        if ($lambda instanceof Closure) $lambda();
    } catch (\Exception $e) {
        $thrown = $e;
    }
    builder()->pop_group();
    if ($thrown) {
        throw $e;
    }
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

function hide() {
    builder()->hide();
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
