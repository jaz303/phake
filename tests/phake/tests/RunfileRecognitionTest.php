<?php namespace phake\tests;

use phake\Builder;
use phake\tests\VfsTestCase;

class RunfileRecognitionTest extends VfsTestCase {
    function setUp() {
        $this->builder = new Builder;
    }

    function test_should_recognize_camelcase_phakefile_as_runfile() {
        $filepath = $this->vfsFile('Phakefile');
        $this->assertEquals(
            $this->builder->resolve_runfile($this->vfsPath()),
            $filepath
        );
    }

    function test_should_recognize_camelcase_phakefile_dot_php_as_runfile() {
        $filepath = $this->vfsFile('Phakefile.php');
        $this->assertEquals(
            $this->builder->resolve_runfile($this->vfsPath()),
            $filepath
        );
    }

    function test_should_recognize_lowercase_phakefile_as_runfile() {
        $filepath = $this->vfsFile('phakefile');
        $this->assertEquals(
            $this->builder->resolve_runfile($this->vfsPath()),
            $filepath
        );
    }

    function test_should_recognize_lowercase_phakefile_dot_php_as_runfile() {
        $filepath = $this->vfsFile('phakefile.php');
        $this->assertEquals(
            $this->builder->resolve_runfile($this->vfsPath()),
            $filepath
        );
    }
}
