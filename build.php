#!/usr/bin/env php
<?php
$phar = new Phar('phake.phar');
$phar->buildFromDirectory('.');
$phar->setStub("#!/usr/bin/env php
<?php
Phar::mapPhar('phake.phar');
require_once 'phar://phake.phar/stub.php';
__HALT_COMPILER();
?>");
rename('phake.phar', 'phake');
chmod('phake', 0755);
