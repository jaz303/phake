<?php

use phake\TaskNotFoundException;

/**
 * Tests of phake\TaskNotFoundException
 *
 * @covers phake\TaskNotFoundException
 */
class TaskNotFoundExceptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test factory method
     *
     * @covers phake\TaskNotFoundException::create
     */
    public function testCreate()
    {
        $e = TaskNotFoundException::create('foo');
        $this->assertInstanceOf('phake\TaskNotFoundException', $e);
        $this->assertEquals('Task "foo" not found', $e->getMessage());
    }

    /**
     * Test getTaskName
     *
     * @covers phake\TaskNotFoundException::getTaskName
     */
    public function testGetTaskName()
    {
        $e = TaskNotFoundException::create('foo');
        $this->assertEquals('foo', $e->getTaskName());
    }
}
