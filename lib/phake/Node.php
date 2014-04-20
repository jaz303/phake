<?php

namespace phake;

class Node
{
    private $parent;
    private $name;
    private $hidden     = false;

    private $deps       = array();
    private $desc       = null;
    private $has_run    = false;

    private $before     = array();
    private $lambdas    = array();
    private $after      = array();

    private $children   = array();

    public function __construct(Node $parent = null, $name = '') {
        $this->parent = $parent;
        $this->name = $name;
    }

    public function get_name() {
        $name = '';

        $parent = $this->parent;
        while ($parent !== null && $parent->parent !== null) {
            $name = $parent->name . ':' . $name;
            $parent = $parent->parent;
        }

        return $name . $this->name;
    }

    public function get_parent() {
        return $this->parent;
    }

    public function get_root() {
        $root = $this;

        while ($root->parent !== null) {
            $root = $root->parent;
        }

        return $root;
    }

    public function child_with_name($task_name) {
        $parts = explode(':', $task_name);

        $task = $this;
        foreach ($parts as $part) {
            if (!isset($task->children[$part])) {
                $task->children[$part] = new Node($task, $part);
            }
            $task = $task->children[$part];
        }

        return $task;
    }

    public function add_before($closure) { $this->before[]  = $closure; }
    public function add_lambda($closure) { $this->lambdas[] = $closure; }
    public function add_after($closure)  { $this->after[]   = $closure; }

    public function is_hidden() {
        return $this->hidden;
    }

    public function hide() {
        $this->hidden = true;
    }

    public function has_description() {
        return $this->desc !== null;
    }

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

    public function invoke(Application $application) {
        foreach ($this->get_dependencies() as $t) $t->invoke($application);

        if ($this->has_run) {
            return;
        }

        foreach ($this->before  as $t) $t($application);
        foreach ($this->lambdas as $t) $t($application);
        foreach ($this->after   as $t) $t($application);

        $this->has_run = true;
    }

    public function get_dependencies() {
        $deps = array();

        foreach ($this->deps as $depName) {
            $task = $this->parent->get_task($depName);
            $deps[$task->get_name()] = $task;
        }

        return $deps;
    }

    public function has_dependencies() {
        return !!$this->get_dependencies();
    }

    public function has_body() {
        return !!$this->lambdas;
    }

    public function get_task($task_name) {
        if ($task_name[0] != ':') {
            
            $parts = explode(':', $task_name);

            $task = $this;
            foreach ($parts as $part) {
                if (isset($task->children[$part])) {
                    $task = $task->children[$part];
                } else {
                    $task = null;
                    break;
                }
            }

            if ($task !== null) {
                return $task;
            } else if ($this->parent) {
                return $this->parent->get_task($task_name);
            } else {
                throw new TaskNotFoundException;
            }

        } else {
            return $this->get_root()->get_task(substr($task_name, 1));
        }
    }

    public function is_visible() {
        return (!$this->is_hidden() && ($this->has_body() || $this->has_dependencies()));
    }

    public function get_tasks() {
        $tasks = array();

        foreach ($this->children as $child) {
            $tasks[$child->get_name()] = $child;
            $tasks += $child->get_tasks();
        }

        return $tasks;
    }
}
