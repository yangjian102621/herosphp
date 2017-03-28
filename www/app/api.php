<?php
/**
 * api入口程序
 * @author yangjian
 * @email yangjian102621@gmail.com
 * @date 2017-03-16
 */
require_once __DIR__."/server.php";
define('RESTFUL_API', false); //是否开启使用 restful api
Herosphp::api(); //启动初始化应用程序

