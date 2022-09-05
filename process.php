<?php

declare(strict_types=1);

use herosphp\core\Config;
use herosphp\GF;
use Workerman\Worker;

require 'boot.php';

// start process worker
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

Worker::runAll();
