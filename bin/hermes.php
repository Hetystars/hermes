<?php

defined('IN_PHAR') or define('IN_PHAR', (bool)\Phar::running(false));
defined('RUNNING_ROOT') or define('RUNNING_ROOT', realpath(getcwd()));
defined('HERMES_ROOT') or define('HERMES_ROOT', IN_PHAR ? \Phar::running() : realpath(getcwd()));

$file = HERMES_ROOT . '/vendor/autoload.php';
if (file_exists($file)) {
    require $file;
} else {
    die("include composer autoload.php fail\n");
}

if (file_exists(HERMES_ROOT . '/bootstrap.php')) {
    require_once HERMES_ROOT . '/bootstrap.php';
}

$args = $argv;
//trim first command
array_shift($args);

(new \Hermes\Core\HermesApplication())->run($args);

