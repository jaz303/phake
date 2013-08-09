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

    public function invoke($task_name, Node $relative_to = null) {
        $this->resolve($task_name, $relative_to)->invoke($this);
    }

    public function clear() {
        $this->root = new Node(null, '');
    }

    public function reset() {
        $this->root->reset();
    }

    public function resolve($task_name, Node $relative_to = null) {
        if ($task_name[0] != ':') {
            if ($relative_to) {
                try {
                    return $relative_to->resolve(explode(':', $task_name));
                } catch (TaskNotFoundException $tnfe) {}
            }
        } else {
            $task_name = substr($task_name, 1);
        }
        return $this->root->resolve(explode(':', $task_name));
    }

    public function get_task_list() {
        $list = array();
        $this->root->fill_task_list($list);
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
        return $this->args[$k];
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
