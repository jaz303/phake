<?php

task('bleem', 'foo', function() {});
task('baz', 'bleem', function() {});
task('foo', 'baz', function() {});
