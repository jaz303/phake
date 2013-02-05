<?php
desc('Show some colors');
task('colors', function() {
    writeln('Running a test of color codes...');
    writeln(
        red('Red star,'), "\n",
        green('green leaf,'), "\n",
        blue('blue sky,'), "\n",
        yellow('yellow stone,'), "\n",
        cyan('cyanide,'), "\n",
        black('black hole,'), "\n",
        purple('purple rain,'), "\n",
        white('"bolded text"', true),
        green("and a text with \nnew line!")
    );
});

desc('Dump all args');
task('args', function($app) {
    echo "Arguments:\n";
    foreach ($app as $k => $v) write(yellow($k), '=', green($v));
});

desc('Load the application environment');
task('environment', function() {
    echo "I am the outer environment. I should run first.\n";
});

desc('Initialises the database connection');
task('database', function() {
    echo "I am initialising the database...\n";
});

group('test', function() {
    
    // 'environment' dependency for this task is resolved locally to
    // task in same group. There is no 'database' task defined in this
    // group so it drops back to a search of the root group.
    desc('Run the unit tests');
    task('units', 'environment', ':environment', 'database', function() {
        echo "Running unit tests...\n";
    });
    
    // another level of nesting; application object is passed to all
    // executing tasks
    group('all', function() {
        desc('Run absolutely every test everywhere!');
        task('run', 'test:units', 'colors', function($application) {
            echo "All tests complete! ($application)\n";
        });
    });

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

task('default', 'test:all:run');
