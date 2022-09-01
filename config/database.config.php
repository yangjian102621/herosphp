<?php
return [
    'default' => [
        'driver' => env_config('DB_DEFAULT_DRIVER', 'mysql'),
        'host' => env_config('DB_DEFAULT_HOST', '127.0.0.1'),
        'port' => env_config('DB_DEFAULT_PORT', '3306'),
        'database' => env_config('DB_DEFAULT_DATABASE', 'test'),
        'username' => env_config('DB_DEFAULT_USERNAME', 'root'),
        'password' => env_config('DB_DEFAULT_PASSWORD', '12345678'),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
    ],
];
