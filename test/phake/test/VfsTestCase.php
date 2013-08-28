<?php namespace phake\test;

use PHPUnit_Framework_TestCase;
use org\bovigo\vfs\vfsStream;

class VfsTestCase extends PHPUnit_Framework_TestCase {
    function vfsFile($filename) {
        vfsStream::setup('test', null, [ $filename => '' ]);
        return vfsStream::url("test/$filename");
    }

    function vfsPath($path = '') {
        if (strlen($path) > 0)
            $path = "/$path";

        return vfsStream::url("test$path");
    }
}

