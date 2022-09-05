<?php

declare(strict_types=1);

use herosphp\ClientApp;

require 'boot.php';

define('RUN_CLI_MODE', true);

// Start client command line App
ClientApp::run($argc, $argv);
