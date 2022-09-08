<?php

declare(strict_types=1);

use herosphp\GF;
use herosphp\utils\FileUtil;
use herosphp\WebApp;
use Workerman\Worker;

require 'boot.php';

define('RUN_WEB_MODE', true);

//opcache
Worker::$onMasterReload = static function () {
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

FileUtil::makeFileDirs(dirname(GF::getAppConfig('worker_log_path')));
//set worker log
Worker::$pidFile = GF::getAppConfig('pid_path');
Worker::$logFile = GF::getAppConfig('worker_log_path');

// Start Web Application worker
WebApp::run();

Worker::runAll();
