<?php

namespace phake;

class Node
{
    private $parent;
    private $name;

    private $deps       = array();
    private $desc       = null;
    private $has_run    = false;

    private $before     = array();
    private $lambdas    = array();
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

    public function add_before($closure) { $this->before[]  = $closure; }
    public function add_lambda($closure) { $this->lambdas[] = $closure; }
    public function add_after($closure)  { $this->after[]   = $closure; }

    public function get_description() {
        return $this->desc;
    }

    public function set_description($d) {
        $this->desc = $d;
    }

    public function add_dependency($taskname) {
        $this->deps[] = $taskname;
    }

    public function reset() {
        $this->has_run = false;

        foreach ($this->children as $c) $c->reset();
    }

    public function invoke($application) {
        foreach ($this->deps as $d) $application->invoke($d, $this->get_parent());

        if ($this->has_run) {
            return;
        }

        foreach ($this->before  as $t) $t($application);
        foreach ($this->lambdas as $t) $t($application);

        foreach ($this->children as $c) {
            $c->invoke($application);
        }

        foreach ($this->after   as $t) $t($application);

        $this->has_run = true;
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
