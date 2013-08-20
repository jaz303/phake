<?php

use phake\Builder;
use phake\Application;

class BuilderTest extends TestCase
{
    public function testConstructor()
    {
        $application = new Application();

        $builder = new Builder($application);

        $this->assertSame($application, $builder->get_application());
    }

    /**
     * @expectedException Exception
     */
    public function testInvalidPath()
    {
        $builder = new Builder();

        $builder->load_runfile('does not exist');
    }

    public function testEmpty()
    {
        $builder = new Builder();

        $builder->load_runfile($this->getFixture('Empty.php'));

        // assert that there are no tasks
    }

    public function testBuilder()
    {
        $builder = new Builder();

        $builder->load_runfile($this->getFixture('Default.php'));

        $this->assertSame($builder, builder());

        // assert there's a single task "default"
    }
}
