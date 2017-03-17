<?php

namespace gmodel;

use gmodel\utils\ControllerFactory;
use gmodel\utils\DBFactory;
use gmodel\utils\ModelFactory;
use gmodel\utils\ServiceFactory;
use gmodel\utils\simple_html_dom;

require_once "utils/simple_html_dom.php";
require_once "utils/DBFactory.class.php";
require_once "utils/ModelFactory.class.php";
require_once "utils/ServiceFactory.class.php";
require_once "utils/ControllerFactory.class.php";

/**
 * 根据database.xml文档创建数据库。同时生成Model, Dao, Service层
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
class GModel {

    /**
     * 创建数据库
     * @param $options
     */
    public static function createDatabase($options) {
        return DBFactory::createDatabase($options);
    }

    /**
     * 创建数据表
     * @param $options
     */
    public static function createTable($options) {

        //创建数据表
        if ( !$options['xmlpath'] ) {
            return tprintError("Please specified the --xmlpath option");
        }
        if ( strpos($options['xmlpath'], '/') === false ) {
            $options['xmlpath'] = APP_PATH."build/{$options['xmlpath']}.xml";
        }
        if ( !file_exists($options['xmlpath']) ) {
            return tprintError("File not found '{$options['xmlpath']}'");
        }
        $xml = new simple_html_dom(file_get_contents($options['xmlpath']));
        DBFactory::createTables($xml);
    }

    /**
     * 创建模型
     * @param $options
     */
    public static function createModel($options) {
        return ModelFactory::create($options);
    }

    /**
     * 创建服务
     * @param $options
     */
    public static function createService($options) {
        return ServiceFactory::create($options);
    }

    /**
     * 创建控制器
     * @param $options
     */
    public static function createController($options) {
        return ControllerFactory::create($options);
    }

    /**
     * 下划线转驼峰
     * @param $str
     * @return string
     */
    public static function underline2hump($str) {

        $str = trim($str);
        if ( strpos($str, "_") === false ) return $str;

        $arr = explode("_", $str);
        $__str = $arr[0];
        for( $i = 1; $i < count($arr); $i++ ) {
            $__str .= ucfirst($arr[$i]);
        }
        return $__str;
    }


}
