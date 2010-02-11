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
?>