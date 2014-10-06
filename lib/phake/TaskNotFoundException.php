<?php

namespace phake;

use Exception;

class TaskNotFoundException extends Exception
{
    /**
     * Task name
     *
     * @var string
     */
    private $taskName;

    /**
     * Factory method for creating new exceptions
     *
     * @param string    $taskName name of task which is not found
     * @param int       $code     exception code
     * @param Exception $previous previous exception used for the exception chaining
     *
     * @return TaskNotFoundException
     */
    public static function create($taskName, $code = 0, Exception $previous = null)
    {
        $message = sprintf('Task "%s" not found', $taskName);
        if (version_compare(PHP_VERSION, '5.4', '<')) {
            $exception = new self($message, $code, $previous);
        } else {
            $exception = new static($message, $code, $previous);
        }
        $exception->taskName = $taskName;
        return $exception;
    }

    /**
     * Return name of not founded task
     *
     * @return string
     */
    public function getTaskName()
    {
        return $this->taskName;
    }
}
