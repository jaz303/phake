<?php

use phake\Builder;
use phake\CycleDetector;

class CycleDetectorTest extends TestCase
{
    public function testCycleDetection()
    {
        $builder = new Builder();
        $builder->load_runfile($this->getFixture('Cycle.php'));
        $root_node = $builder->get_application()->root();
        $cycles = (new CycleDetector($root_node))->get_cycles();
        $this->assertCount(1, $cycles);
        $this->assertCount(3, $cycles[0]);

        $task_names = array_map(function($task) { return $task->get_name(); }, $cycles[0]);
        $this->assertContains('bleem', $task_names);
        $this->assertContains('baz', $task_names);
        $this->assertContains('foo', $task_names);
    }
}
