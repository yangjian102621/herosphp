<?php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__, 5) . DIRECTORY_SEPARATOR);
}

if (!defined('APP_PATH')) {
    define('APP_PATH', BASE_PATH . 'app/');
}

if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', BASE_PATH . 'config/');
}

if (!defined('RUNTIME_PATH')) {
    define('RUNTIME_PATH', BASE_PATH . 'runtime/');
}

if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', BASE_PATH . 'public/');
}

// define framework version
define('X_POWER', 'Herosphp/4.0.1');
