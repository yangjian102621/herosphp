<?php

declare(strict_types=1);

use herosphp\core\Config;
use herosphp\GF;
use herosphp\WebApp;
use Workerman\Worker;

require 'boot.php';

// Start Web Application worker
WebApp::run();

Worker::runAll();
