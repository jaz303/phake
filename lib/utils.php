<?php
namespace phake;

function resolve_runfile($directory) {
    $runfiles = array('Phakefile', 'Phakefile.php');
    do {
        foreach ($runfiles as $r) {
            $candidate = $directory . '/' . $r;
            if (file_exists($candidate)) {
                return $candidate;
            }
        }
        if ($directory == '/') {
            throw new \Exception("No Phakefile found");
        }
        $directory = dirname($directory);
    } while (true);
}

function load_runfile($file) {
    require $file;
}

function fatal($exception, $message = null) {
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
?>