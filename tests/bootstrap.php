<?php

require __DIR__ . '/../lib/includes.php';
// require __DIR__ . '/../vendor/autoload.php';

abstract class TestCase extends PHPUnit_Framework_TestCase
{
    protected function getFixture($name)
    {
        return __DIR__ . '/fixtures/' . $name;
    }
}
