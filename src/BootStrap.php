<?php
/**
 * HerosPHP 框架入口类
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.0.0
 */

namespace herosphp;

// 检测PHP环境
if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');
define('FRAME_VERSION', '3.0.0'); //框架版本
date_default_timezone_set(TIME_ZONE); //设置默认时区

use herosphp\core\Loader;
use herosphp\core\WebApplication;
use herosphp\api\GeneralApi;
use herosphp\api\RestfulApi;

class BootStrap
{

    /**
     * 框架启动入口函数
     */
    public static function run()
    {
        //根据环境配置来获取相应的配置
        $appConfigs = Loader::config('app', 'env.' . ENV_CFG);
        $application = WebApplication::getInstance();
        $application->execute($appConfigs);
    }

    /**
     * 客户端入口
     */
    public static function runCli($argv, $argc)
    {
        error_reporting(E_ERROR);
        $params = getCliArgs($argv, $argc);
        if ($params['debug'] == 'true') {
            $_SERVER['debug'] = true;
        }
        Artisan::run($params);
    }
}
