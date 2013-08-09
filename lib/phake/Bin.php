<?php

namespace phake;

use phake\Builder;
use phake\OptionParser;
use phake\Utils;
use phake\TaskNotFoundException;
use Exception;

class Bin
{
    public function execute($args) {
        try {

            //
            // Defaults

            $action     = 'invoke';
            $task_names = array('default');
            $trace      = false;
            $runfile    = false;

            array_shift($args);
            $parser = new OptionParser($args);
            foreach ($parser->get_options() as $option => $value) {
                switch ($option) {
                    case 't':
                    case 'trace':
                        $trace = true;
                        break;
                    case 'T':
                    case 'tasks':
                        $action = 'list';
                        break;
                    case 'f':
                    case 'file':
                        $runfile = $value;
                        break;
                    default:
                        throw new Exception("Unknown command line option '$option'");
                }
            }

            $cli_args = array();
            $cli_task_names = array();
            foreach ($parser->get_non_options() as $option) {
                if (strpos($option, '=') > 0) {
                    $cli_args[] = $option;
                } else {
                    $cli_task_names[] = $option;
                }
            }

            $cli_args = Utils::parse_args($cli_args);

            if (count($cli_task_names)) {
                $task_names = $cli_task_names;
            }

            //
            // Locate runfile
            if (!$runfile) {
                $runfile = resolve_runfile(getcwd());
                $directory = dirname($runfile);

                if (!@chdir($directory)) {
                    throw new Exception("Couldn't change to directory '$directory'");
                } else {
                    echo "(in $directory)\n";
                }
            }

            load_runfile($runfile);

            //
            // Go, go, go

            $application = builder()->get_application();
            /* @var $application Application */
            $application->set_args($cli_args);
            $application->reset();

            switch ($action) {
                case 'list':
                    $task_list = $application->get_task_list();
                    if (count($task_list)) {
                        $max = max(array_map('strlen', array_keys($task_list)));
                        foreach ($task_list as $name => $desc) {
                            echo str_pad($name, $max + 4) . $desc . "\n";
                        }
                    }
                    break;
                case 'invoke':
                    foreach ($task_names as $task_name) {
                        $application->invoke($task_name);
                    }
                    break;
            }

        } catch (TaskNotFoundException $tnfe) {
            fatal($tnfe, "Don't know how to build task '$task_name'\n");
        } catch (Exception $e) {
            fatal($e);
        }
    }
}
