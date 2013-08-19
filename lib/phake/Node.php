<?php

namespace phake;

class Node
{
    private $parent;
    private $name;

    private $before     = array();
    private $tasks      = array();
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
            $name .= $parent->name . ':';
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

    public function child_with_name($name) {
        if (!isset($this->children[$name])) {
            $this->children[$name] = new Node($this, $name);
        }
        return $this->children[$name];
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

    public function invoke(Application $application) {
        foreach ($this->get_dependencies() as $t) $t->invoke($application);
        foreach ($this->before as $t) $t->invoke($application);
        foreach ($this->tasks as $t) $t->invoke($application);
        foreach ($this->after as $t) $t->invoke($application);
    }

    public function fill_task_list(&$out, $prefix = '') {
        foreach ($this->get_tasks() as $name => $node) {
            $out[$name] = $node->get_description();
        }
    }

    public function get_dependencies()
    {
        $deps = array();

        foreach ($this->dependencies() as $depName) {
            $task = $this->get_task($depName);
            $deps[$task->get_name()] = $task;
        }

        return $deps;
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
            }
        } else {
            $task_name = substr($task_name, 1);
        }

        $root = $this->get_root();
        if ($root === $this) {
            throw new TaskNotFoundException;
        }

        return $root->get_task($task_name);
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
