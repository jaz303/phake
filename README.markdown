phake - Rake/Make for PHP 5.3 [![Build Status](https://travis-ci.org/jaz303/phake.png?branch=master)](https://travis-ci.org/jaz303/phake)
=============================

&copy; 2010 Jason Frame [ [jason@onehackoranother.com](mailto:jason@onehackoranother.com) / [@jaz303](http://twitter.com/jaz303) ]  
Released under the MIT License.

A wee clone of Ruby's `rake` for PHP 5.3. Uses closures for ultimate coolness.

Questions abut `phake`? Come and chat in `#phake` on Freenode!

Usage
-----

  * Download
  * Create a `Phakefile` in the current directory or a parent directory
  * Invoke `./phake task:name` to invoke task or `./phake -T` to list defined tasks
  
Defining Tasks
--------------

Define tasks like this:

    task('dependency1', function() {
        echo "i will run first!\n";
    });
    
    task('dependency2', function() {
        echo "i will run second!\n";
    });

    task('task_name', 'dependency1', 'dependency2', function() {
        echo "i will run last!\n";
    });
    
This task would be invoked from the command line by `./phake task_name`

Task bodies are optional if you want to create some sort of "super-task" that just invokes a bunch of others:

    task('foo', 'dep1', 'dep2');

And multiple bodies can be added to tasks, all of which will be executed when the task is invoked:

    task('foo', function() { echo "task work item 1\n"; });
    task('foo', function() { echo "task work item 2\n"; });

Grouping Tasks
--------------

Like Rake, we can group tasks:

    group('db', function() {
        task('init', function() {
            echo "i'm initialising the database\n";
        });
    });
    
This would be invoked by `./phake db:init`

Describing Tasks
----------------

Call `desc()` immediately before defining a task to set its description:

    desc("Initialises the database");
    task('db_init', function() { echo "oh hai it's a database\n"; });
    
Output from `./phake -T`:

    db_init    Initialises the database
  
After/Before Blocks
-------------------

Sometimes you may want to specify that some code should run before or after a task (distinct from dependencies), a bit like Capistrano. Phake supports this:

    before('foo', function() { ... });
    after('baz:bar', function() { ... });
    
Task Arguments
--------------

Phake allows arguments to specified on the command line:

    # Execute task `quux` with the given args
    ./phake quux name=Jason city=Glasgow
    
This format must be matched exactly; do not put spaces between `=` and the argument name/value. If you need to put spaces in the argument value, place the entire assignment in quotes.

Arguments are made available to tasks by the application object's `ArrayAccess` implementation:

    task('task_with_args', function($app) {
        $name = $app['name'];
        $city = $app['city'];
        // do some stuff...
    });

Aborting Execution
------------------

To abort execution of a task sequence, simply throw an exception.

    desc('Demonstrate failure');
    task('fail', function() {
        throw new Exception;
    });
    
Running `phake fail` will yield:

    - jason@disco phake % ./bin/phake fail
    (in /Users/jason/dev/projects/phake)
    aborted!
    Exception 

    (See full trace by running task with --trace)

A Somewhat More Complete Example
--------------------------------

This is what a complete `Phakefile` might look like. It also highlights some of the more complex name resolution issues arising when dealing with groups.

    <?php
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
            task('run', 'test:units', function($application) {
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
    ?>
    
Here's the output from `./phake` (implied task to run is `default`):

    jason@ratchet phake [master*] $ ./phake
    (in /Users/jason/dev/projects/phake)
    I am the outer environment. I should run first.
    I am the inner environment. I should run second.
    I am initialising the database...
    Running unit tests...
    Running a second batch of unit tests...
    All tests complete! (<phake\Application>)
    
And the corresponding output from `phake -T`:

    jason@ratchet phake [master*] $ ./phake -T
    (in /Users/jason/dev/projects/phake)
    database        Initialises the database connection
    environment     Load the application environment
    test:all:run    Run absolutely every test everywhere!
    test:units      Run the unit tests

Bash Autocompletion
-------------------

Bashkim Isai has created [`phake-autocomplete`](https://github.com/bashaus/phake-autocomplete), a bash-completion script for phake task names.

Known Bugs
----------

No cyclic dependency checking.
