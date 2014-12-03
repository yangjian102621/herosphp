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

include APP_FRAME_PATH.'functions.php';      //包含框架全局函数
include APP_FRAME_PATH.'core/Loader.class.php';     //包含资源加载器

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
     * 框架启动入口函数
     */
    public static function run() {

        self::_loadBaseLib();   //加载框架核心类

        date_default_timezone_set(TIME_ZONE);  //设置默认时区

        if ( APP_DEBUG ) {
            Debug::start();
            error_reporting(E_ALL);
            //设置捕获系统异常
            set_error_handler(array('herosphp\core\Debug', 'customError'));
        } else {
            error_reporting(0);
            ini_set("display_errors", "Off");
        }

        $configs = Loader::config();    //加载系统全局配置
        $application = WebApplication::getInstance();
        $application->execute($configs);

        //Debug::printMessage();
    }

    /**
     * 加载核心层库函数
     * @return void
     */
    private static function _loadBaseLib() {
        self::$LIB_CLASS = array(
            'herosphp\http\HttpRequest'          => 'http.HttpRequest',
            'herosphp\core\WebApplication'       => 'core.WebApplication',
            'herosphp\core\Debug'       => 'core.Debug',
            'herosphp\core\Loader'       => 'core.Loader',
            'herosphp\core\Template'       => 'core.Template',
            'herosphp\core\Controller'       => 'core.Controller',
            'herosphp\exception\HeroException'       => 'exception.HeroException',
            'herosphp\exception\DBException'       => 'exception.DBException',
            'herosphp\utils\FileUtils'       => 'utils.FileUtils',
            'herosphp\utils\ArrayUtils'       => 'utils.ArrayUtils',
            'herosphp\utils\AjaxResult'       => 'utils.AjaxResult',
            'herosphp\utils\WebUtils'       => 'utils.WebUtils',
            'herosphp\db\DBFactory'       => 'db.DBFactory',
            'herosphp\db\SQL'       => 'db.SQL',
            'herosphp\model\C_Model'       => 'model.C_Model',
            'herosphp\cache\CacheFactory'       => 'cache.CacheFactory',
            'HomecommonAction'  => 'core.HomecommonAction',
            'AjaxResult'        => 'public.AjaxResult');
    }

    /**
     * 自动加载系统框架类
     * @param $className
     */
    public static function autoLoad($className) {
        Loader::import(self::$LIB_CLASS[$className], IMPORT_FRAME, EXT_PHP);
    }
    
}

//自动加载核心类
function __autoload( $className ) {
    Herosphp::autoLoad($className);
}
?>