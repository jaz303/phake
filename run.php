#!/usr/local/bin/php
<?php
require dirname(__FILE__) . '/lib/phake.php';
require dirname(__FILE__) . '/lib/global_helpers.php';
require dirname(__FILE__) . '/lib/option_parser.php';

define('RUNFILE', 'Phakefile');

try {
    
	//
    // Defaults
    
    $action     = 'invoke';
    $task_names = array('default');
    $trace      = false;
    
    //
    // Parse opts, getopt() in PHP blows goats as it can't update argv...

	$equivalence = array('t' => 'trace', 'T' => 'tasks');
	$args = $GLOBALS['argv'];
	array_shift($args);
	$parser = new \Phake\OptionParser($args, $equivalence);
	while ($option = $parser->next_option()) {
		switch ($option) {
			case 'trace':
				$trace = true;
				break;
			case 'tasks':
				$action = 'list';
				break;
			default:
				throw new Exception("Unknown command line option\n");
		}
	}
	
	$remainder = $parser->remainder();
	if (count($remainder)) $task_names = $remainder;

    //
    // Locate runfile

	$directory = getcwd();
	do {
		if (file_exists($directory . '/' . RUNFILE)) {
			break;
		} elseif (__is_root($directory)) {
			throw new Exception("No Phakefile found");
		} else {
			$directory = dirname($directory);
		}
	} while (true);
    
    if (!@chdir($directory)) {
        throw new Exception("Couldn't change to directory '$directory'");
    } else {
        echo "(in $directory)\n";
    }

    __load_runfile(RUNFILE);

    //
    // Go, go, go
    
    $application = \Phake\Application::instance();
    $application->reset();
    
    switch ($action) {
        case 'list':
            $task_list = $application->get_task_list();
			$max = max(array_map('strlen', array_keys($task_list)));
			foreach ($task_list as $name => $desc) {
				echo str_pad($name, $max + 4) . $desc . "\n";
			}
            break;
        case 'invoke':
            foreach ($task_names as $task_name) {
                $application->invoke($task_name);
            }
            break;
    }

} catch (\Phake\TaskNotFoundException $tnfe) {
    __fatal($tnfe, "Don't know how to build task '$task_name'\n");
} catch (Exception $e) {
    __fatal($e);
}

function __load_runfile($file) {
    require $file;
}

function __fatal($exception, $message = null) {
    echo "aborted!\n";
    if (!$message) $message = $exception->getMessage();
    if (!$message) $message = get_class($exception);
    echo $message . "\n\n";
    global $trace;
    if ($trace) {
        echo $exception->getTraceAsString() . "\n";
    } else {
        echo "(See full trace by running task with --trace)\n";
    }
    die(1);
}

function __is_root($dir) {
	return $dir == '/';
}
?>