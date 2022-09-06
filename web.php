<?php

declare(strict_types=1);

use herosphp\WebApp;
use Workerman\Worker;

require 'boot.php';
require 'Monitor.php';

define('RUN_WEB_MODE', true);

// Start Web Application worker
WebApp::run();

Worker::runAll();
