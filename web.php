<?php

declare(strict_types=1);

use herosphp\WebApp;
use Workerman\Worker;

require 'boot.php';
require 'Monitor.php';

define('RUN_WEB_MODE', true);

//opcache
\Workerman\Worker::$onMasterReload = static function () {
    if (function_exists('opcache_get_status') && function_exists('opcache_invalidate')) {
        if ($status = opcache_get_status()) {
            if (isset($status['scripts']) && $scripts = $status['scripts']) {
                foreach (array_keys($scripts) as $file) {
                    opcache_invalidate($file, true);
                }
            }
        }
    }
};

// Start Web Application worker
WebApp::run();

Worker::runAll();
