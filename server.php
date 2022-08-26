<?php

use herosphp\WebApp;
use Workerman\Worker;

require_once 'vendor/autoload.php';
require_once 'file_watcher.php';

if (!defined('APP_PATH')) {
    define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'app/');
}
if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'config/');
}
if (!defined('RUNTIME_PATH')) {
    define('RUNTIME_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'runtime/');
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'public/');
}

// start the web application
WebApp::run();

// run all the workers of workerman
Worker::runAll();
