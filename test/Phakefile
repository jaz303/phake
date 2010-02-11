<?php
function p($t) {
  echo $t . "\n";
}

group('bleem', function() {
    desc('bleem:baz');
    task('baz', ':bleem', function() { p(1); });
    after('baz', function() { p(2); });
});

desc('bleem');
task('bleem', function() { p(0); });

after('bleem:baz', function() { p(3); });

desc('foo');
task('foo', function() { p(5); });

desc('bar');
task('bar', function() { p(8); });
task('foo', function() { p(6); });

before('foo', function() { p(4); });
after('foo', function() { p(7); });

task('foo', 'bleem:baz');

desc('default');
task('default', 'foo');
task('default', 'bar');
?>