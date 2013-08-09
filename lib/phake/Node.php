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

    public function child_with_name($name) {
        if (!isset($this->children[$name])) {
            $this->children[$name] = new Node($this, $name);
        }
        return $this->children[$name];
    }

    public function resolve(array $task_name_parts) {
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

    public function invoke(Application $application) {
        foreach ($this->dependencies() as $d) $application->invoke($d, $this->get_parent());
        foreach ($this->before as $t) $t->invoke($application);
        foreach ($this->tasks as $t) $t->invoke($application);
        foreach ($this->after as $t) $t->invoke($application);
    }

    public function fill_task_list(&$out, $prefix = '') {
        foreach ($this->get_tasks() as $name => $node) {
            if ($desc = $node->get_description()) {
                $out[$name] = $desc;
            }
        }
    }

    public function get_tasks() {
        $tasks = array();

        foreach ($this->children as $child) {
            if ($child->tasks) {
                $tasks[$child->get_name()] = $child;
            }
            $tasks += $child->get_tasks();
        }

        return $tasks;
    }
}
