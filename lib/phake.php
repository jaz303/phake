<?php
namespace phake;

class TaskNotFoundException extends \Exception {};
class TaskCollisionException extends \Exception {};

class Utils
{
    public static function parse_args(array $args) {
        $out = array();
        $pos = 0;
        foreach ($args as $arg) {
            list($k, $v) = explode('=', $arg);
            if (!isset($v)) {
                $out[$pos++] = $k;
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }
}

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
    
    public function invoke($task_name, $relative_to = null) {
        $this->resolve($task_name, $relative_to)->invoke($this);
    }
    
    public function reset() {
        $this->root->reset();
    }
    
    public function resolve($task_name, $relative_to = null) {
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

class Node
{
    private $parent;
    private $name;

    private $before     = array();
    private $tasks      = array();
    private $after      = array();
    
    private $children   = array();
    
    public function __construct($parent, $name) {
        $this->parent = $parent;
        $this->name = $name;
    }
    
    public function get_name($name) {
        return $this->name;
    }
    
    public function get_parent() {
        return $this->parent;
    }
    
    public function child_with_name($name) {
        if (!isset($this->children[$name])) {
            $this->children[$name] = new Node($this, $name);
        }
        return $this->children[$name];
    }
    
    public function resolve($task_name_parts) {
        if (count($task_name_parts) == 0) {
            return $this;
        } else {
            $try = array_shift($task_name_parts);
            if (isset($this->children[$try])) {
                return $this->children[$try]->resolve($task_name_parts);
            } else {
                throw new TaskNotFoundException;
            }
        }
    }
    
    public function before(Task $task) { $this->before[] = $task; }
    public function task(Task $task) { $this->tasks[] = $task; }
    public function after(Task $task) { $this->after[] = $task; }
    
    public function dependencies() {
        $deps = array();
        foreach ($this->tasks as $t) {
            $deps = array_merge($deps, $t->dependencies());
        }
        return $deps;
    }
    
    public function get_description() {
        foreach ($this->tasks as $t) {
            if ($desc = $t->get_description()) return $desc;
        }
        return null;
    }
    
    public function reset() {
        foreach ($this->before as $t) $t->reset();
        foreach ($this->tasks as $t) $t->reset();
        foreach ($this->after as $t) $t->reset();
        foreach ($this->children as $c) $c->reset();
    }
    
    public function invoke($application) {
        foreach ($this->dependencies() as $d) $application->invoke($d, $this->get_parent());
        foreach ($this->before as $t) $t->invoke($application);
        foreach ($this->tasks as $t) $t->invoke($application);
        foreach ($this->after as $t) $t->invoke($application);
    }
    
    public function fill_task_list(&$out, $prefix = '') {
        foreach ($this->children as $name => $child) {
            if ($desc = $child->get_description()) {
                $out[$prefix . $name] = $desc;
            }
            $child->fill_task_list($out, "{$prefix}{$name}:");
        }
    }
}

// Single unit of work
class Task
{
    private $lambda;
    private $deps;
    private $desc       = null;
    private $has_run    = false;
    
    public function __construct($lambda = null, $deps = array()) {
        $this->lambda = $lambda;
        $this->deps = $deps;
    }
    
    public function get_description() {
        return $this->desc;
    }
    
    public function set_description($d) {
        $this->desc = $d;
    }
    
    public function dependencies() {
        return $this->deps;
    }
    
    public function reset() {
        $this->has_run = false;
    }
    
    public function invoke($application) {
        if (!$this->has_run) {
            if ($this->lambda) {
                $lambda = $this->lambda;
                $lambda($application);
            }
            $this->has_run = true;
        }
    }
}
?>
