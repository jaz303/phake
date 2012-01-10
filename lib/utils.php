<?php
namespace phake {

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
}

namespace {

    /**
     * Fails the build, by throwing an exception
     * @param m the error message
     */
    function fail($m) {
        throw new Exception($m);
    }

    /**
     * 'echo' ends with a new line
     */
    function println() {
        echo implode(func_get_args()), "\n";
    }

    /**
     * mkdir -p
     */
    function mk_dir($dir, $mod=0755, $failOnErr=true) {
        if (!is_dir($dir) && !mkdir($dir, $mod, true) && $failOnErr) fail("mkdir '$dir' failed");
    }

    /**
     * 'copy' with overwirting options
     */
    function cp_file($src, $dest, $overwrite=false) {
        if (!file_exists($dest) || $overwrite) copy($src, $dest);
    }

    /**
     * generate text file with a given template, with simple variable replaceing.
     */
    function gen_file($dest, $tpl, $vars=array(), $overwrite=false) {
        if (!file_exists($dest) || $overwrite) {
            $content = file_get_contents($tpl);

            foreach ($vars as $var => $value) {
                $content = str_replace($var, $value, $content);
            }

            file_put_contents($dest, $content);
        }
    }
}
?>