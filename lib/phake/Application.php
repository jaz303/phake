<?php

namespace phake;

class Application implements \ArrayAccess, \IteratorAggregate
{
    private $root;
    private $args;

    public function __construct() {
        $this->root = new Node(null, '');
        $this->args = array();
    }

    public function root() {
        return $this->root;
    }

    public function invoke($task_name) {
        $this->get_task($task_name)->invoke($this);
    }

    public function clear() {
        $this->root = new Node(null, '');
    }

    public function reset() {
        $this->root->reset();
    }

    public function get_task($task_name) {
        return $this->root->get_task($task_name);
    }

    public function get_tasks() {
        return $this->root->get_tasks();
    }

    public function get_task_list() {
        $list = array();
        foreach ($this->get_tasks() as $name => $task) {
            if ($task->is_visible()) {
                $list[$name] = $task->get_description();
            }
        }
        ksort($list);
        return $list;
    }

    public function __toString() {
        return '<' . get_class($this) . '>';
    }

    //
    // ArrayAccess/IteratorAggregate - for argument support

    public function set_args(array $args) {
        $this->args = $args;
    }

    public function offsetExists($k) {
        return array_key_exists($k, $this->args);
    }

    public function offsetGet($k) {
        return isset($this->args[$k]) ? $this->args[$k] : null;
    }

    public function offsetSet($k, $v) {
        $this->args[$k] = $v;
    }

    public function offsetUnset($k) {
        unset($this->args[$k]);
    }

    public function getIterator() {
        return new \ArrayIterator($this->args);
    }
}
