<?php
require_once 'vendor/autoload.php';
use herosphp\utils\InstanceTrait;

class A
{
    use InstanceTrait;
}

$a1 = A::getInstance();
$a2 = A::getInstance();
$a3 = new A();
$a4 = A::getInstance(true);


var_export($a1 === $a2);
echo PHP_EOL;
var_export($a1 === $a3);
echo PHP_EOL;
var_export($a1 === $a4);
