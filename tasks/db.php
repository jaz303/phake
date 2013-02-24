<?php

task('init',function(){
	writeln("Testing :) DB INIT");
});

// duplicate group definitions are merged
group('test', function() {
    
    // duplicate task definitions are merged
    // (although the first description takes precedence when running with -T)
    desc("You won't see this description");
    task('units', function() {
        echo "Running a second batch of unit tests...\n";
    });
    
    // use ':environment' to refer to task in root group
    // we currently have no cyclic dependency checking, you have been warned.
    task('environment', ':environment', function() {
        echo "I am the inner environment. I should run second.\n";
    });
    
});