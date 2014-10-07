<?php

namespace phake;

use \SplStack;

/**
 * Detects cycles in the directed-graph of dependencies using Tarjan's Algorithm
 */
class CycleDetector
{
    private $root_node;
    // A list of nodes marked by index and lowlink and on-stack status
    private $marked_nodes;
    private $current_index;
    private $visited_nodes;
    private $connected_components;

    public function __construct($root_node) {
        $this->root_node = $root_node;
    }

    private function initialize() {
        $this->current_index = 0;
        $this->marked_nodes = array();
        $this->visited_nodes = new SplStack();
        $this->connected_components = array();
    }

    public function get_cycles() {
        $this->initialize();
        foreach ($this->root_node->get_tasks() as $name => $task) {
            if ( ! $this->has_marked_node($task) ) {
                $this->find_strongly_connected_components($task);
            }
        }

        return array_filter($this->connected_components, function ($component) {
            return count($component) > 1;
        });
    }

    private function find_strongly_connected_components($first_task) {
        $first_marked_node =& $this->mark_node($first_task);
        $this->current_index ++;
        $this->visited_nodes->push($first_task);
        foreach ($first_task->get_dependencies() as $name => $second_task) {
            $second_task_key = $this->task_key($second_task);
            if ( ! $this->has_marked_node($second_task) ) {
                $this->find_strongly_connected_components($second_task);
                $second_marked_node =& $this->get_marked_node($second_task);
                $first_marked_node['lowlink'] = min(
                    $first_marked_node['lowlink'], $second_marked_node['lowlink']
                );
            } else if ($this->marked_nodes[$second_task_key]['on_stack'] === true) {
                $second_marked_node =& $this->get_marked_node($second_task);
                $first_marked_node['lowlink'] = min(
                    $first_marked_node['lowlink'], $second_marked_node['index']
                );
            }
        }

        if ($first_marked_node['lowlink'] == $first_marked_node['index']) {
            $connected_component = array();
            do {
                $next_node = $this->visited_nodes->pop();
                $next_marked_node =& $this->get_marked_node($next_node);
                $connected_component[] = $next_node;
                $next_marked_node['on_stack'] = false;
            } while ($first_marked_node['task_node'] !== $next_node);
            $this->connected_components[] = $connected_component;
        }
    }

    private function has_marked_node($task_node) {
        $task_key = $this->task_key($task_node);
        return isset($this->marked_nodes[$task_key]);
    }

    private function &mark_node($task_node) {
        $task_key = $this->task_key($task_node);
        $this->marked_nodes[$task_key] = array(
            'task_node' => $task_node,
            'index'     => $this->current_index,
            'lowlink'   => $this->current_index,
            'on_stack'  => true
        );
        return $this->marked_nodes[$task_key];
    }

    private function &get_marked_node($task_node) {
        $task_key = $this->task_key($task_node);
        return $this->marked_nodes[$task_key];
    }

    private function task_key($task_node) {
        return spl_object_hash($task_node);
    }
}
