<?php
require_once 'vendor/autoload.php';
define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
define('APP_PATH', BASE_PATH . 'app/');
define('CONFIG_PATH', BASE_PATH . 'config/');
define('RUNTIME_PATH', BASE_PATH . 'runtime/');
define('PUBLIC_PATH', BASE_PATH . 'public/');
\herosphp\utils\Redis::set('herosphp-dev', 4.0);
\herosphp\utils\Redis::get('herosphp-dev');
