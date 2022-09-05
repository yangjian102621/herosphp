<?php

declare(strict_types=1);

use herosphp\WebApp;
use Workerman\Worker;

require 'boot.php';
require 'Monitor.php';

// Start Web Application worker
WebApp::run();

Worker::runAll();
