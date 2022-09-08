<?php

// WebApp configs

return [
    'debug' => true,
    'error_reporting' => E_ALL,
    'timezone' => 'Asia/Shanghai',

    'template' => ['rules' => [], 'skin' => 'default'],

    //app log
    'log_path' => RUNTIME_PATH.'logs/',
    // worker process pid path
    'pid_path' => RUNTIME_PATH.'worker.pid',
    // worker log path
    'worker_log_path' => RUNTIME_PATH.'logs/worker.log',

    // server configs
    'server' => [
        'name' => 'WebApp',
        'listen' => 'http://0.0.0.0:2345',
        'context' => [],
        'worker_count' => 1,
        'reloadable' => 'true',
    ],

    'machine_id' => 0x01 // machine id for generate UUID
];
