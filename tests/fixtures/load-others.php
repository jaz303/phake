<?php

group('main', function () {
    load_runfile(__DIR__ . '/Default.php');
});

group('empty', function () {
    load_runfile(__DIR__ . '/Empty.php');
});

group('sub', function () {
    load_runfile(__DIR__ . '/load-sub.php');
});

task('link', 'main:default');