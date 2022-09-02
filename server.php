<?php

use herosphp\WebApp;
use Workerman\Worker;

require_once 'vendor/autoload.php';
require_once 'file_watcher.php';

define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', BASE_PATH . 'app/');
define('CONFIG_PATH', BASE_PATH . 'config/');
define('RUNTIME_PATH', BASE_PATH . 'runtime/');
define('PUBLIC_PATH', BASE_PATH . 'public/');

// start the web application
WebApp::run();

// run all the workers of workerman
Worker::runAll();
