<?php

task('a:b', function () { echo "a:b\n"; });

group('b', function () {
    task('a', function () { echo "b:a\n"; });
});

task('default', 'a:b', 'b:a');