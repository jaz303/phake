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

    public function testOrder()
    {
        $builder = new Builder();

        $builder->load_runfile($this->getFixture('Order.php'));

        $this->expectOutputString(<<<EOF
0
1
2
3
4
5
6
7
8

EOF
);

        $builder->get_application()->invoke('default');
    }

    public function testGroups()
    {
        $builder = new Builder();

        $builder->load_runfile($this->getFixture('groups.php'));

        $this->expectOutputString(<<<EOF
a:b
b:a

EOF
);
        $builder->get_application()->invoke('default');
    }

    public function testArguments()
    {
        $builder = new Builder();

        $builder->load_runfile($this->getFixture('arguments.php'));

        $this->expectOutputString(<<<EOF
first
phake\Application
phake\Node
first:test
phake\Application
phake\Node

EOF
        );
        $builder->get_application()->invoke('default');
    }

    public function testBuilderGroupException()
    {
        $builder = new Builder();

        try {
            $builder->add_group('exception', function() {
                throw new \Exception();
            });
            $this->fail('No exception thrown');
        }
        catch (Exception $e) {
            // exception caught. Now check our context is still correct
        }

        $that = $this;
        $builder->add_group('ok', function($app, $node) use ($that) {
            $that->assertInstanceOf('phake\Node', $node);
            $that->assertEquals('ok', $node->get_name());
        });
    }
}
