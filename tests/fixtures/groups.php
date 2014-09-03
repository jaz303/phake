<?php

group('y:z', function() {
  task('a', function () { echo "y:z:a\n"; });
});

task('a:b', function () { echo "a:b\n"; });

group('b', function () {
    task('a', function () { echo "b:a\n"; });
});

task('default', 'a:b', 'b:a', 'y:z:a');
