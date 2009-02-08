<?php
namespace Phake;

class TaskNotFoundException extends \Exception {};
class TaskCollisionException extends \Exception {};

class Application
{
    private static $instance = null;
    
    public static function instance() {
        if (!isset(self::$instance)) {
            self::$instance = new Application;
        }
        return self::$instance;
    }
    
    private $root;
    
    public function __construct() {
        $this->root = new Group($null, '');
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
}

class Node
{
    private $parent;
    private $name;
    
    protected $children = array();
    
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
}

class Group extends Node
{
    public function add_group($name) {
        if (!isset($this->children[$name])) {
            $this->children[$name] = new Group($this, $name);
        } elseif (!($this->children[$name] instanceof Group)) {
            throw new TaskCollisionException("Can't create group '$name', already defined as something else");
        }
        return $this->children[$name];
    }
    
    public function add_task($name, Task $t) {
        if (!isset($this->children[$name])) {
            $this->children[$name] = new TaskWrapper($this, $name);
        } elseif (!($this->children[$name] instanceof TaskWrapper)) {
            throw new TaskCollisionException("Can't create task '$name', already defined as something else");
        }
        $this->children[$name]->push($t);
    }
    
    public function reset() {
        foreach ($this->children as $c) $c->reset();
    }
    
    public function fill_task_list(&$out, $prefix = '') {
        foreach ($this->children as $name => $child) {
            if ($child instanceof TaskWrapper) {
                if ($desc = $child->get_description()) {
                    $out[$prefix . $name] =  $desc;
                }
            } else {
                $child->fill_task_list($out, "{$prefix}{$name}:");
            }
        }
    }
}

class TaskWrapper extends Node
{
    private $tasks = array();
    
    public function push($task) {
        $this->tasks[] = $task;
    }
    
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
        foreach ($this->tasks as $t) $t->reset();
    }
    
    public function invoke($application) {
        foreach ($this->dependencies() as $d) $application->invoke($d, $this->get_parent());
        foreach ($this->tasks as $t) $t->invoke($application);
    }

}

class Task extends Node
{
    private $lambda;
    private $dependencies;
    private $description    = null;
    private $has_run        = false;
    
    public function __construct($lambda = null, $dependencies = array()) {
        $this->lambda       = $lambda;
        $this->dependencies = $dependencies;
    }
    
    public function get_description() {
        return $this->description;
    }
    
    public function set_description($d) {
        $this->description = $d;
    }
    
    public function dependencies() {
        return $this->dependencies;
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