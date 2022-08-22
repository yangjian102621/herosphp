<?php

use herosphp\WebApp;
use Workerman\Worker;

require_once 'vendor/autoload.php';
// require_once 'file_watcher.php';

if (!defined('APP_PATH')) define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'app/');
if (!defined('CONFIG_PATH')) define('CONFIG_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'config/');

// start the web application
WebApp::run();

// run all the workers of workerman
Worker::runAll();
