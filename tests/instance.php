<?php
require_once 'vendor/autoload.php';

use herosphp\core\Config;
use herosphp\utils\InstanceTrait;

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__ . DIRECTORY_SEPARATOR);
}

if (!defined('CONFIG_PATH')) {
    define('CONFIG_PATH', __DIR__ . DIRECTORY_SEPARATOR . 'config/');
}

class A
{
    use InstanceTrait;
}

$a1 = A::getInstance();
$a2 = A::getInstance();
$a3 = new A();
$a4 = A::getInstance(true);

var_export($a1 === $a2);
echo '-----------------'.PHP_EOL;
var_export($a1 === $a3);
echo '-----------------'.PHP_EOL;
var_export($a1 === $a4);
echo '-----------------'.PHP_EOL;

var_dump(Config::get('redis', null, []));
