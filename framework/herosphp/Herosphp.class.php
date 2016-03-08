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

//定义当前应用根目录
define('APP_PATH', APP_ROOT.APP_NAME."/");
include APP_FRAME_PATH.'Heros.const.php'; //引入系统常量文件
include APP_FRAME_PATH.'functions.core.php';//包含框架全局函数
include APP_ROOT.'functions.php'; //包含公共函数页面
include APP_FRAME_PATH.'core/Loader.class.php';//包含资源加载器

use herosphp\core\Loader;
use herosphp\core\WebApplication;
use herosphp\core\Debug;

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

        self::_loadBaseLib();   //加载框架核心类

        date_default_timezone_set(TIME_ZONE);  //设置默认时区

        if ( APP_DEBUG ) {
            Debug::start();
            error_reporting(E_ALL);
            ini_set("display_errors", "On");
            //设置捕获系统异常
            set_error_handler(array('herosphp\core\Debug', 'customError'));
        } else {
            error_reporting(0);
            ini_set("display_errors", "Off");
        }

        $configs = Loader::config('system', 'root');    //加载系统全局配置
        $appConfigs = Loader::config('app'); //加载当前应用的配置信息
        //将应用的配置信息覆盖系统的全局配置信息
        $configs = array_merge($configs, $appConfigs);
        $application = WebApplication::getInstance();
        $application->execute($configs);

        //Debug::printMessage();
    }

    /**
     * 执行客户端的php任务
     * @param string $taskName  任务名称
     */
    public static function runClient( $taskName = null ) {

        if ( $taskName == null || $taskName == '' ) {
            tprintError('请传入需要执行的任务名称！');
            die();
        }

        //加载框架核心类
        self::_loadBaseLib();
        //设置默认时区
        date_default_timezone_set(TIME_ZONE);
        //设置时间用不超时
        set_time_limit(0);

        //设置错误等级
        error_reporting(E_ALL & ~E_STRICT & ~E_NOTICE & ~E_WARNING);

        Loader::import($taskName, IMPORT_CLIENT, '.php');

    }

    /**
     * 加载核心层库函数
     * @return void
     */
    private static function _loadBaseLib() {
        self::$LIB_CLASS = array(
            'herosphp\http\HttpRequest'          => 'http.HttpRequest',
            'herosphp\http\HttpClient'          => 'http.HttpClient',
            'herosphp\core\WebApplication'       => 'core.WebApplication',
            'herosphp\core\Debug'       => 'core.Debug',
            'herosphp\core\Loader'       => 'core.Loader',
            'herosphp\core\Template'       => 'core.Template',
            'herosphp\core\Controller'       => 'core.Controller',
            'herosphp\exception\HeroException'       => 'exception.HeroException',
            'herosphp\exception\DBException'       => 'exception.DBException',
            'herosphp\utils\FileUtils'       => 'utils.FileUtils',
            'herosphp\utils\FileUpload'       => 'utils.FileUpload',
            'herosphp\utils\ArrayUtils'       => 'utils.ArrayUtils',
            'herosphp\utils\AjaxResult'       => 'utils.AjaxResult',
            'herosphp\utils\WebUtils'       => 'utils.WebUtils',
            'herosphp\utils\HashUtils'       => 'utils.HashUtils',
            'herosphp\utils\ImageThumb'       => 'utils.ImageThumb',
            'herosphp\utils\ImageWater'       => 'utils.ImageWater',
            'herosphp\utils\Page'       => 'utils.Page',
            'herosphp\utils\PHPZip'       => 'utils.PHPZip',
            'herosphp\utils\Smtp'       => 'utils.Smtp',
            'herosphp\utils\VerifyCode'       => 'utils.VerifyCode',
            'herosphp\db\DBFactory'       => 'db.DBFactory',
            'herosphp\db\SQL'       => 'db.SQL',
            'herosphp\model\C_Model'       => 'model.C_Model',
            'herosphp\filter\Filter'       => 'filter.Filter',
            'herosphp\cache\CacheFactory'       => 'cache.CacheFactory',
            'herosphp\bean\Beans'  => 'bean.Beans',
            'herosphp\listener\WebApplicationListenerMatcher'  => 'listener.WebApplicationListenerMatcher',
            'herosphp\session\Session'  => 'session.Session');

        self::$APP_CLASS = array(
            'admin\action\CommonAction'        => 'admin.action.CommonAction',
            'common\action\CommonAction'        => 'common.action.CommonAction',
            'media\action\MediaAction'        => 'media.action.MediaAction',
            'site\action\AbstractAction'        => 'site.action.AbstractAction',
            'common\action\NeedLoginAction'        => 'common.action.NeedLoginAction',
            'client\tools\result\AbstractResult'        => 'common.client.result.AbstractResult',
            'client\tools\result\JsonResult'        => 'common.client.result.JsonResult',
            'client\tools\result\XmlResult'        => 'common.client.result.XmlResult',
        );
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
        }
    }

}

//自动加载核心类
spl_autoload_register(array('Herosphp', 'autoLoad'));
