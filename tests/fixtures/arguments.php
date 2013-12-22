<?php

group('first', function($app, $node) {
    echo $node->get_name() . "\n";
    echo get_class($app) . "\n";
    echo get_class($node) . "\n";

    task('test', function($app, $node) {
        echo $node->get_name() . "\n";
        echo get_class($app) . "\n";
        echo get_class($node) . "\n";
    });
});

task('default', 'first:test');
