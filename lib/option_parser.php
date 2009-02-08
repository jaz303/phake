<?php
namespace Phake;

class OptionParser
{
    private $args;
    private $equiv;
    private $array_pos      = 0;
    private $string_pos     = null;
    
    public function __construct($args, $equiv = array()) {
        $this->args         = $args;
        $this->equiv        = array();
    }
    
    public function next_option() {
        if ($this->array_pos >= count($args)) {
            return false;
        }
    }
    
    public function next_value() {
        
    }
    
    public function remainder() {
        
    }
}
?>