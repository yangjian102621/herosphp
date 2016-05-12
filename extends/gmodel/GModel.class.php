<?php

namespace gmodel;

use gmodel\utils\DaoFactory;
use gmodel\utils\DBFactory;
use gmodel\utils\ModelFactory;
use gmodel\utils\ServiceFactory;
use gmodel\utils\simple_html_dom;

require_once "utils/simple_html_dom.php";
require_once "utils/DBFactory.class.php";
require_once "utils/ModelFactory.class.php";
require_once "utils/DaoFactory.class.php";
require_once "utils/ServiceFactory.class.php";

/**
 * 根据database.xml文档创建数据库。同时生成Model, Dao, Service层
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
class GModel {

    /**
     * @var simple_html_dom
     */
    private static $XML = null; //xml对象

    private $opt;   //操作
    private $module; //模块

    //构造函数
    public function __construct($module, $opt) {

        if ( !$module || !$opt ) {
            tprintError("请指定模块和操作！"); die();
        }
        //获取并解析xml文档
        $xmlFilePath = APP_PATH."xml/{$module}.xml";
        self::$XML = new simple_html_dom(file_get_contents($xmlFilePath));

        $this->module = $module;
        $this->opt = $opt;
    }

    //执行创建
    public function execute() {

        switch ( $this->opt ) {
            case "table":
                DBFactory::create(self::$XML);
                break;

            case "model":
                ModelFactory::create(self::$XML);
                break;

            case "dao":
                DaoFactory::create(self::$XML);
                break;

            case "service":
                ServiceFactory::create(self::$XML);
                break;

            case "all":
                DBFactory::create(self::$XML);
                ModelFactory::create(self::$XML);
                DaoFactory::create(self::$XML);
                ServiceFactory::create(self::$XML);
                break;
        }

    }

    /**
     * 下划线转驼峰
     * @param $str
     * @return string
     */
    public static function underline2hump($str) {

        if ( strpos($str, "_") === false ) return $str;

        $arr = explode("_", $str);
        $__str = $arr[0];
        for( $i = 1; $i < count($arr); $i++ ) {
            $__str .= ucfirst($arr[$i]);
        }
        return $__str;
    }


}
