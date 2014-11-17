<?php
/*---------------------------------------------------------------------
 * HerosPHP 资源加载器类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\core;

class Loader {

    /**
     * 已经导入的class文件
     * @var array
     */
    private static $IMPORTED_FILES = array();

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
     * 加载配置信息
     * @param string $key 配置文件名称key
     * @param string $section 配置文档所属片区|模块
     * @return array
     */
    public static function config( $key=null, $section=null ) {

        $configDir = APP_CONFIG_PATH;
        if ( $section != null ) $configDir .= $section.'/';
        if ( $key != null ) {
            $configFile = $configDir.$key.'.config.php';
            return include $configFile;
        } else {
            chdir($configDir);
            $configFiles = glob("*.config.php");
            $configs = array();
            foreach ( $configFiles as $file ) {
                $tempConfig = include APP_CONFIG_PATH.$file;
                $configs = array_merge($configs, $tempConfig);
            }
            return $configs;
        }
    }
} 