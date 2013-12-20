<?php

/**
 * This file includes the entire phake library in one go.
 * The benefit is that an autoloader is not needed
 */

require_once __DIR__ . '/phake/Application.php';
require_once __DIR__ . '/phake/Bin.php';
require_once __DIR__ . '/phake/Builder.php';
require_once __DIR__ . '/phake/Node.php';
require_once __DIR__ . '/phake/OptionParser.php';
require_once __DIR__ . '/phake/TaskCollisionException.php';
require_once __DIR__ . '/phake/TaskNotFoundException.php';
require_once __DIR__ . '/phake/Utils.php';
