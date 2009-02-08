<?php
$GLOBALS['__CONTEXT__'] = array(\Phake\Application::instance()->root());
$GLOBALS['__DESC__'] = null;

function push_task($name, $task) {
    if ($GLOBALS['__DESC__'] !== null) {
        $task->set_description($GLOBALS['__DESC__']);
        $GLOBALS['__DESC__'] = null;
    }
    active_group()->add_task($name, $task);
}

function active_group() {
    return $GLOBALS['__CONTEXT__'][count($GLOBALS['__CONTEXT__']) - 1];
}

function task() {
    $deps = func_get_args();
    $name = array_shift($deps);
    if ($deps[count($deps) - 1] instanceof Closure) {
        $work = array_pop($deps);
    } else {
        $work = null;
    }
    push_task($name, new \Phake\Task($work, $deps));
}

function group($name, $lambda = null) {
    $GLOBALS['__CONTEXT__'][] = active_group()->add_group($name);
    if ($lambda !== null) $lambda();
    array_pop($GLOBALS['__CONTEXT__']);
}

function desc($desc) {
    $GLOBALS['__DESC__'] = $desc;
}
?>