<?php
/*---------------------------------------------------------------------
 * HerosPHP 框架入口类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

define('FRAME_VERSION', '3.0.0'); //框架版本
require_once APP_FRAME_PATH.'Heros.const.php'; //引入系统常量文件
require_once APP_FRAME_PATH.'functions.core.php';//包含框架全局函数
require_once APP_PATH . 'functions.php'; //包含公共函数页面
require_once APP_FRAME_PATH.'core/Loader.class.php';//包含资源加载器

use herosphp\core\Loader;
use herosphp\core\WebApplication;
use herosphp\Artisan;

class Herosphp {

    /**
     * 需要自动加载的框架基本类库
     * @var array
     */
    private static $LIB_CLASS = array();

    /**
     * 需要自动加载的app服务类
     * @var array
     */
    private static $APP_CLASS = array();

    /**
     * 框架启动入口函数
     */
    public static function run() {

        self::init();

        //根据环境配置来获取相应的配置,如果没有的话，就加载默认的
        if( defined('ENV_CFG') ){
            $appConfigs = Loader::config('app', 'env.'.ENV_CFG); //加载当前应用的配置信息
        }else{
            $appConfigs = Loader::config('app'); //加载当前应用的配置信息
        }
        $application = WebApplication::getInstance();
        $application->execute($appConfigs);

    }

    /**
     * 客户端入口
     */
    public static function artisan() {
        self::init();
        Artisan::run();
    }

    /**
     * 初始化
     * @param string $taskName  任务名称
     */
    public static function init() {

        self::_loadBaseLib();   //加载框架核心类

        date_default_timezone_set(TIME_ZONE);  //设置默认时区

        if ( APP_DEBUG ) {
            error_reporting(ERROR_LEVEL);
            ini_set("display_errors", "On");
        } else {
            error_reporting(0);
            ini_set("display_errors", "Off");
        }

        //加载框架核心类
        self::_loadBaseLib();
        //设置默认时区
        date_default_timezone_set(TIME_ZONE);
        //设置时间用不超时
        if ( RUN_CLI ) {
            set_time_limit(0);
        }

//        $className = ucfirst($taskName).'Task';
//        Loader::import("tasks.{$className}", IMPORT_CLIENT);
//        $clazz = new ReflectionClass("tasks\\{$className}");
//        $method = $clazz->getMethod('run');
//        $method->invoke($clazz->newInstance());

    }

    /**
     * 加载核心层库函数
     * @return void
     */
    private static function _loadBaseLib() {
        self::$LIB_CLASS = array(
            'herosphp\Artisan'          => 'Artisan',
            'herosphp\http\HttpRequest'          => 'http.HttpRequest',
            'herosphp\http\HttpClient'          => 'http.HttpClient',
            'herosphp\core\WebApplication'       => 'core.WebApplication',
            'herosphp\core\Log'       => 'core.Log',
            'herosphp\core\Loader'       => 'core.Loader',
            'herosphp\core\AppError'       => 'core.AppError',
            'herosphp\core\Template'       => 'core.Template',
            'herosphp\core\Controller'       => 'core.Controller',

            'herosphp\exception\HeroException'       => 'exception.HeroException',
            'herosphp\exception\DBException'       => 'exception.DBException',
            'herosphp\exception\UnSupportedOperationException'       => 'exception.UnSupportedOperationException',

            'herosphp\files\FileUtils'       => 'files.FileUtils',
            'herosphp\files\FileUpload'       => 'files.FileUpload',
            'herosphp\files\PHPZip'       => 'files.PHPZip',

            'herosphp\utils\ArrayUtils'       => 'utils.ArrayUtils',
            'herosphp\utils\AjaxResult'       => 'utils.AjaxResult',
            'herosphp\utils\HashUtils'       => 'utils.HashUtils',
            'herosphp\utils\Page'       => 'utils.Page',
            'herosphp\utils\ModelTransformUtils'   => 'utils.ModelTransformUtils',

            'herosphp\string\StringBuffer'       => 'string.StringBuffer',
            'herosphp\string\StringUtils'       => 'string.StringUtils',

            'herosphp\image\ImageThumb'       => 'image.ImageThumb',
            'herosphp\image\ImageWater'       => 'image.ImageWater',
            'herosphp\image\VerifyCode'       => 'image.VerifyCode',

            'herosphp\web\Smtp'       => 'web.Smtp',
            'herosphp\web\WebUtils'       => 'web.WebUtils',

            'herosphp\db\DBFactory'       => 'db.DBFactory',
            'herosphp\db\mysql\MysqlQueryBuilder'       => 'db.mysql.MysqlQueryBuilder',
            'herosphp\db\mongo\MongoQueryBuilder'       => 'db.mongo.MongoQueryBuilder',

            'herosphp\model\C_Model'       => 'model.C_Model',
            'herosphp\model\ShardingRouterModel'       => 'model.ShardingRouterModel',
            'herosphp\model\SimpleShardingModel'       => 'model.SimpleShardingModel',
            'herosphp\model\MongoModel'       => 'model.MongoModel',

            'herosphp\lock\SemSynLock'       => 'lock.SemSynLock',
            'herosphp\lock\FileSynLock'       => 'lock.FileSynLock',
            'herosphp\lock\SynLockFactory'       => 'lock.SynLockFactory',

            'herosphp\filter\Filter'       => 'filter.Filter',

            'herosphp\cache\CacheFactory'       => 'cache.CacheFactory',
            'herosphp\cache\utils\RedisUtils'       => 'cache.utils.RedisUtils',

            'herosphp\bean\Beans'  => 'bean.Beans',
            'herosphp\listener\WebApplicationListenerMatcher'  => 'listener.WebApplicationListenerMatcher',
            'herosphp\session\Session'  => 'session.Session');

        //获取自动加载类配置
        self::$APP_CLASS = Loader::config("autoload");
    }

    /**
     * 自动加载系统框架类和app公共类
     * @param $className
     */
    public static function autoLoad($className) {
        if ( self::$LIB_CLASS[$className] ) {
            Loader::import(self::$LIB_CLASS[$className], IMPORT_FRAME, EXT_PHP);
        } else {
            Loader::import(self::$APP_CLASS[$className], IMPORT_APP, EXT_PHP);
            Loader::import(self::$APP_CLASS[$className], IMPORT_CUSTOM, EXT_PHP);
        }
    }

}

//自动加载核心类
spl_autoload_register(array('Herosphp', 'autoLoad'));
