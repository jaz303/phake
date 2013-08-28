<?php namespace phake\test;

use phake;
use phake\test\VfsTestCase;

class RunfileRecognitionTest extends VfsTestCase {
    function test_should_recognize_camelcase_phakefile_as_runfile() {
        $filepath = $this->vfsFile('Phakefile');
        $this->assertEquals(
            phake\resolve_runfile($this->vfsPath()),
            $filepath
        );
    }

    function test_should_recognize_camelcase_phakefile_dot_php_as_runfile() {
        $filepath = $this->vfsFile('Phakefile.php');
        $this->assertEquals(
            phake\resolve_runfile($this->vfsPath()),
            $filepath
        );
    }

    function test_should_recognize_lowercase_phakefile_as_runfile() {
        $filepath = $this->vfsFile('phakefile');
        $this->assertEquals(
            phake\resolve_runfile($this->vfsPath()),
            $filepath
        );
    }

    function test_should_recognize_lowercase_phakefile_dot_php_as_runfile() {
        $filepath = $this->vfsFile('phakefile.php');
        $this->assertEquals(
            phake\resolve_runfile($this->vfsPath()),
            $filepath
        );
    }
}
