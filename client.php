<?php

declare(strict_types=1);

use herosphp\ClientApp;

define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', BASE_PATH . 'app/');
define('CONFIG_PATH', BASE_PATH . 'config/');
define('RUNTIME_PATH', BASE_PATH . 'runtime/');
define('PUBLIC_PATH', BASE_PATH . 'public/');
define('RUN_CLI_MODE', true);

require_once BASE_PATH . 'vendor/autoload.php';

ClientApp::run($argc, $argv);
