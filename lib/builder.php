<?php
namespace phake;

class Builder
{
    public static $global;
    
    private $application;
    private $context;
    private $description;
    
    public function __construct() {
        $this->application = new Application;
        $this->context = $this->application->root();
        $this->description = null;
    }
    
    public function get_application() {
        return $this->application;
    }
    
    public function desc($d) {
        $this->description = $d;
    }
    
    public function add_task($name, $work, $deps) {
        $node = $this->context->child_with_name($name);
        $task = new Task($work, $deps);
        $this->assign_description($task);
        $node->task($task);
    }
    
    public function push_group($name) {
        $this->context = $this->context->child_with_name($name);
    }
    
    public function pop_group() {
        $this->context = $this->context->get_parent();
    }
    
    public function before($name, $lambda) {
        $this->application->resolve($name, $this->context)->before(new Task($lambda));
    }
    
    public function after($name, $lambda) {
        $this->application->resolve($name, $this->context)->after(new Task($lambda));
    }
    
    //
    //
    
    private function assign_description($thing) {
        if ($this->description !== null) {
            $thing->set_description($this->description);
            $this->description = null;
        }
    }
}
?>