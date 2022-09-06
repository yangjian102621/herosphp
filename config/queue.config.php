<?php
return [
    'default' => [
        'host' => 'redis://172.28.1.3:6379',
        'options' => [
            'auth' => null,    // 密码，可选参数
            'db' => 0,      // 数据库
            'max_attempts' => 10, // 消费失败后，重试次数
            'retry_seconds' => 5, // 重试间隔，单位秒
        ],
    ],
];
