<?php

task('a:b', function () { writeln('a:b'); });

group('b', function () {
    task('a', function () { writeln('b:a'); });
});

task('default', 'a:b', 'b:a');