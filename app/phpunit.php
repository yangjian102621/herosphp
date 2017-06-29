<?php
/**
 * 单元测试入口程序
 * @author yangjian
 * @since v3.0.0
 */
require_once __DIR__."/server.php";
define('PHP_UNIT' , true); //开启phpunit模式
\herosphp\BootStrap::run(); //启动应用程序
