<?php
/*---------------------------------------------------------------------
 * HerosPHP 框架入口类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

include APP_FRAME_PATH.'functions.php';      //包含框架全局函数

class Herosphp {

    /**
     * 已经导入的class文件
     * @var array
     */
    private static $IMPORTED_FILES = array();

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
            error_reporting(E_ALL);
            //设置捕获系统异常
            set_error_handler(array("herosphp\core\Debug", 'customError'));
        } else {
            error_reporting(0);
            ini_set("display_errors", "Off");
        }

        $configs = self::getConfigs();
        __print($configs);

    }

    /**
     * 获取当所有配置信息
     */
    public static function getConfigs() {

        chdir(APP_CONFIG_PATH);
        $configFiles = glob("*.config.php");
        $configs = array();
        foreach ( $configFiles as $file ) {
            $tempConfig = include APP_CONFIG_PATH.$file;
            $configs = array_merge($configs, $tempConfig);
        }
        return $configs;
	}
    
    /**
     * 加载一个类或者加载一个包
     * 如果加载的包中有子文件夹不进行循环加载
     * 参数格式：'article.model.articleModel'
     * article.model.articleModel 相对的路径信息
     * 如果不填写应用名称 ，例如‘article.model.articleModel’，那么加载路径则相对于默认的应用路径
     *
     * 加载一个类的参数方式：'article.model.articleModel'
     * 加载一个包的参数方式：'article.service.*'
     * @param $classPath
     * @param $type
     * @param $extension
     * @return bool
     */
    public static function import( $classPath, $type = IMPORT_APP, $extension=EXT_PHP ) {
    	
        if ( !$classPath ) return;
        //如果该文件已经导入了，就不再导入
        $classKey = $classPath.'_'.$type.'_'.$extension;
        if ( isset(self::$IMPORTED_FILES[$classKey]) )  return;

        //组合文件路径
        $path = '';
        switch ( $type ) {
            case IMPORT_APP :
                $path = APP_PATH;
                break;

            case IMPORT_FRAME :
                $path = APP_FRAME_PATH;
                break;

            case IMPORT_CUSTOM :
                $path = APP_ROOT;
                break;

            default:
                return false;
        }

        $classPathInfo = explode('.', $classPath);
        $classPath = str_replace('.', '/', $classPath);
        if ( $classPathInfo[count($classPathInfo)-1] == '*' ) {     //加载包

            $dir = $path.$classPath;
            chdir($dir);
            $classFiles = glob('*'.$extension);
            foreach ($classFiles as $file ) {
                include $dir.'/'.$file;
            }

        } else {    //包含文件
            include $path.$classPath.$extension;
        }

        self::$IMPORTED_FILES[$classKey] = 1;
        return true;
    }

    /**
     * 加载核心层库函数
     * @return void
     */
    private static function _loadBaseLib() {
        self::$LIB_CLASS = array(
            'herosphp\core\HttpRequest'          => 'core.HttpRequest',
            'herosphp\core\Webapplication'       => 'core.Webapplication',
            'herosphp\core\Debug'       => 'core.Debug',
            'HomecommonAction'  => 'core.HomecommonAction',
            'AjaxResult'        => 'public.AjaxResult');
    }

    /**
     * 自动加载系统框架类
     * @param $className
     */
    public static function autoLoad($className) {
        self::import(self::$LIB_CLASS[$className], IMPORT_FRAME, EXT_PHP);
    }
    
}

//自动加载核心类
function __autoload( $className ) {
    Herosphp::autoLoad($className);
}
?>