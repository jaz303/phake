<?php

namespace phake;

class Builder
{
    public static $global;

    private $application;
    private $target_node;
    private $description;
    private $hidden;

    public function __construct(Application $application = null) {
        if ($application === null) {
            $application = new Application;
        }
        $this->application = $application;
        $this->target_node = $this->application->root();
        $this->description = null;
        $this->hidden = false;
    }

    public function get_application() {
        return $this->application;
    }

    public function desc($d) {
        $this->description = $d;
    }

    public function hide() {
        $this->hidden = true;
    }

    public function clear() {
        $this->application->clear();
        $this->target_node = $this->application->root();
    }

    public function add_task($name, $work, $deps) {
        $node = $this->target_node->child_with_name($name);
        /* @var $node phake\Node */

        if ($work !== null) {
            $node->add_lambda($work);
        }
        foreach ($deps as $dep) {
            $node->add_dependency($dep);
        }
        $this->assign_description($node);
        $this->assign_hidden($node);
    }

    public function push_group($name) {
        $this->target_node = $this->target_node->child_with_name($name);
    }

    public function pop_group() {
        $this->target_node = $this->target_node->get_parent();
    }

    public function before($name, $lambda) {
        $this->target_node->get_task($name)->add_before($lambda);
    }

    public function after($name, $lambda) {
        $this->target_node->get_task($name)->add_after($lambda);
    }

    //
    //

    private function assign_description($thing) {
        if ($this->description !== null && !$thing->has_description()) {
            $thing->set_description($this->description);
        }
        $this->description = null;
    }

    private function assign_hidden($thing) {
        if ($this->hidden) {
            $thing->hide();
        }
        $this->hidden = false;
    }

    public function resolve_runfile($directory) {
        $directory = rtrim($directory, '/') . '/';
        $runfiles = array('Phakefile', 'Phakefile.php');
        do {
            foreach ($runfiles as $r) {
                $candidate = $directory . $r;
                if (file_exists($candidate)) {
                    return $candidate;
                }
            }
            if ($directory == '/') {
                throw new \Exception("No Phakefile found");
            }
            $directory = dirname($directory);
        } while (true);
    }

    public function load_runfile($file) {
        if (!is_file($file)) {
            throw new \Exception('The given path to the Phakefile does not exist');
        }

        require_once __DIR__ . '/../functions.php';
        require_once __DIR__ . '/../term_colors.php';

        // set global reference for builder() helper as used in Phakefiles
        self::$global = $this;

        require $file;
    }
}
