<?php

declare(strict_types=1);

use herosRQueue\Consumer;
use process\AsyncTaskWorker;
use process\CrontabWorker;
use process\Monitor;

return [
    'file_watcher' => [
        'enable' => true,
        'handler' => Monitor::class,
        'reloadable' => true,
        'constructor' => [
            'monitor_dir' => [
                APP_PATH,
                CONFIG_PATH,
            ],
            'monitor_extensions' => [
                'php',
            ],
        ],
    ],

    //仅能1个进程
    'crontab' => [
        'enable' => true,
        'handler' => CrontabWorker::class
    ],

    //大量任务的时候，通过投递到异步任务完成
    'async_worker' => [
        'enable' => true,
        'listen' => 'tcp://127.0.0.1:8182',
        'handler' => AsyncTaskWorker::class,
        'count' => 1
    ],

    'redis-queue' => [
        'enable' => true,
        'handler' => Consumer::class,
        'count' => 1,
        'constructor' => [
            'consumer_dir' => APP_PATH.'queue',
        ],
    ],
];
