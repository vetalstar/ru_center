<?php

define('SYS', 1);

require __DIR__ . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . 'System.php';

spl_autoload_register(array('System', 'auto_load'));

require __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';

if (PHP_SAPI == 'cli')
{
	CLI_Task::factory(CLI_Helper::options())->execute();
}
else
	die('Only CLI support');