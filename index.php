<?php
/**
 * 前台统一入口文件
 */
header("Content-Type:text/html; charset=utf-8");  //设置系统的输出字符为utf-8
define("ROOT", dirname(__FILE__));	//系统根目录
define("DIR_OS", DIRECTORY_SEPARATOR);	//目录分隔符
require ROOT.DIR_OS.'libs'.DIR_OS.'Heros.const.php';    //常量文件
require ROOT.DIR_OS.'libs'.DIR_OS.'Herosphp.class.php';	//包含系统框架的统一入口文件
Herosphp::run();
?>