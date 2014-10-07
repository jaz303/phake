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
    private $taskName = '';

    /**
     * Constructor
     *
     * @param string    $taskName name of task which is not found
     */
    public function __construct($taskName) {
        parent::__construct(sprintf('Task "%s" not found', $taskName));
        $this->taskName = $taskName;
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
