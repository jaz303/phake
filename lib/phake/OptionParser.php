<?php

namespace phake;

//
// getopt() in PHP blows goats as it can't update argv to get non-options.
// and i didn't want to rely on PEAR. hence this disaster.
// you can't specify whether or not options have an argument. instead it's greedy,
// so option always consumes argument if available.
// $ phake -t foo # won't work
// you need to do:
// $ phake -t -- foo
// or fix it yourself =P

class OptionParser
{
    private $args;
    private $index = 0;
    private $options;
    private $non_options;

    public function __construct($args) {
        $this->args = $args;
        $this->parse();
    }

    public function get_options() {
        return $this->options;
    }

    public function get_non_options() {
        return $this->non_options;
    }

    protected function parse() {
        $this->options      = array();
        $this->non_options  = array();
        $last_opt           = null;
        while ($option = $this->next()) {
            if ($option == '--') {
                while ($option = $this->next()) {
                    $this->non_options[] = $option;
                }
            } elseif (preg_match('/^--([\w]+(-[\w]+)*)(=([^\s]+))?$/', $option, $matches)) {
                $this->append_option($matches[1]);
                if (isset($matches[4])) {
                    $this->set_option_value($matches[1], $matches[4]);
                }
            } elseif (preg_match('/^-([a-z0-9])([^\s]+)?$/i', $option, $matches)) {
                $this->append_option($matches[1]);
                if (isset($matches[2])) {
                    $this->set_option_value($matches[1], $matches[2]);
                } else {
                    $last_opt = $matches[1];
                }
            } else {
                if ($last_opt) {
                    $this->set_option_value($last_opt, $option);
                    $last_opt = null;
                } else {
                    $this->non_options[] = $option;
                }
            }
        }
    }

    protected function append_option($option) {
        if (isset($this->options[$option])) {
            $this->options[$option] = (array) $this->options[$option];
            $this->options[$option][] = false;
        } else {
            $this->options[$option] = false;
        }
    }

    protected function set_option_value($option, $value) {
        if (is_array($this->options[$option])) {
            $len = count($this->options[$option]);
            $this->options[$option][$len - 1] = $value;
        } else {
            $this->options[$option] = $value;
        }
    }

    protected function next() {
        if ($this->index == count($this->args)) {
            return false;
        }
        return $this->args[$this->index++];
    }
}
