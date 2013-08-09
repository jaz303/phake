<?php

namespace phake;

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
