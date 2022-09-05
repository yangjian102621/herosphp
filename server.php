<?php

declare(strict_types=1);

use herosphp\core\Config;
use herosphp\GF;
use herosphp\WebApp;
use Workerman\Worker;

define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', BASE_PATH . 'app/');
define('CONFIG_PATH', BASE_PATH . 'config/');
define('RUNTIME_PATH', BASE_PATH . 'runtime/');
define('PUBLIC_PATH', BASE_PATH . 'public/');

require_once BASE_PATH . 'vendor/autoload.php';

// start the web application worker
WebApp::run();

// Windows does not support custom processes.
if (str_contains(PHP_OS, 'WINNT') === false) {
    $processes = Config::get(name: 'process', default: []);
    foreach ($processes as $processName => $config) {
        if (!($config['enable'] ?? false)) {
            continue;
        }
        GF::processRun($processName, $config);
    }
}

// run all the workers of workerman
Worker::runAll();
