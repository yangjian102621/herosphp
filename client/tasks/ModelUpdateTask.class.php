<?php

namespace tasks;

use tasks\interfaces\ITask;
use herosphp\core\Loader;
use xml\simple_html_dom;

Loader::import('tasks.interfaces.ITask', IMPORT_CLIENT);
Loader::import("extends.xml.simple_html_dom", IMPORT_CUSTOM, ".php");
/**
 * 根据database.xml文档创建数据库。同时生成Model, Dao, Service层
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
class ModelUpdateTask implements ITask {

    /**
     * @var simple_html_dom
     */
    private static $XML = null; //xml对象

    public function run() {

        //获取并解析xml文档
        $xmlFilePath = APP_PATH."database.xml";
        self::$XML = new simple_html_dom(file_get_contents($xmlFilePath));

        self::createDatabase();
        //print_r($taskName = $_SERVER['argv']);
    }

    /**
     * 获取数据库连接
     * @param $configs
     * @return \mysqli
     */
    private static function &getDB($configs) {

        $connection = mysqli_connect($configs["dbhost"], $configs["dbuser"], $configs["dbpass"]);
        return $connection;

    }

    /**
     * 创建数据库
     */
    private static function createDatabase() {

        $database = self::$XML->find("database", 0);
        $configs = array(
            "dbhost" => $database->dbhost,
            "dbuser" => $database->dbuser,
            "dbpass" => $database->dbpass,
            "dbname" => $database->dbname,
            "charset" => $database->charset
        );

        $link = self::getDB($configs);

        __print($link);

    }

}
