<?php

use phake\Node;
use phake\TaskNotFoundException;

class NodeTest extends TestCase
{
    public function testRootNode()
    {
        $root = new Node(null, '');

        $this->assertEquals(null, $root->get_description());
        $this->assertEquals('', $root->get_name());
        $this->assertEquals(null, $root->get_parent());
        $this->assertEquals($root, $root->get_root());
        $this->assertEquals(array(), $root->get_dependencies());

        return $root;
    }

    /**
     *
     * @param Node $root
     * @depends testRootNode
     * @expectedException phake\TaskNotFoundException
     */
    public function testInvalidRootGetTask(Node $root)
    {
        $root->get_task('invalid');
    }

    /**
     *
     * @param Node $root
     * @depends testRootNode
     */
    public function testFirstLevelNode(Node $root)
    {
        /* @var $first Node */
        $first = $root->child_with_name('first');

        $this->assertEquals(null, $first->get_description());
        $this->assertEquals('first', $first->get_name());
        $this->assertEquals($root, $first->get_parent());
        $this->assertEquals($root, $first->get_root());
        $this->assertEquals(array(), $first->get_dependencies());

        $this->assertSame($first, $root->child_with_name('first'));
        $this->assertSame($first, $root->get_task('first'));
        $this->assertSame(array('first' => $first), $root->get_tasks());

        return $first;
    }

    /**
     *
     * @param Node $root
     * @param Node $sub
     * @depends testRootNode
     * @depends testFirstLevelNode
     */
    public function testSecondLevelNode(Node $root, Node $first)
    {
        /* @var $second Node */
        $second = $first->child_with_name('second');

        $this->assertEquals('first:second', $second->get_name());
        $this->assertEquals($first, $second->get_parent());
        $this->assertEquals($root, $second->get_root());

        $this->assertSame($second, $first->get_task('second'));
        $this->assertSame($second, $root->get_task('first:second'));
        $this->assertSame($second, $first->get_task(':first:second'));
    }
}
