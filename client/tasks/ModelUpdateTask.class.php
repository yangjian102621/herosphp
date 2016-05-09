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

    private static $debug = true;   //是否开启调试模式

    private static $DB_CONN = null; //数据库连接

    public function run() {

        //获取并解析xml文档
        $xmlFilePath = APP_PATH."database.xml";
        self::$XML = new simple_html_dom(file_get_contents($xmlFilePath));
        $database = self::$XML->find("database", 0);
        $configs = array(
            "dbhost" => $database->dbhost,
            "dbuser" => $database->dbuser,
            "dbpass" => $database->dbpass,
            "dbname" => $database->dbname,
            "charset" => $database->charset
        );

        self::dbConnection($configs); //创建数据库
        self::createDatabase($configs); //创建数据库
        //print_r($taskName = $_SERVER['argv']);
    }

    /**
     * 获取数据库连接
     * @param $configs
     * @return \mysqli
     */
    private static function dbConnection($configs) {

        self::$DB_CONN = mysqli_connect($configs["dbhost"], $configs["dbuser"], $configs["dbpass"]);
        self::$DB_CONN->query("SET names {$configs["charset"]}");
        self::$DB_CONN->query("SET character_set_client = {$configs["charset"]}");
        self::$DB_CONN->query("SET character_set_results = {$configs["charset"]}");

    }

    private static function query($sql) {

        if ( self::$debug ) tprintWarning($sql);

        return self::$DB_CONN->query($sql);
    }

    /**
     * 创建数据库结构
     */
    private static function createDatabase($configs) {

        $sql = "CREATE DATABASE IF NOT EXISTS `{$configs["dbname"]}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        if ( self::query($sql) !== false ) {
            self::query("USE `{$configs["dbname"]}`;");
            tprintOk("创建数据库成功！");
        } else {
            tprintError("创建数据库失败！");
        }

        self::createTables($configs);

    }

    /**
     * 创建表结构
     * @param $configs
     */
    private static function createTables($configs) {

        $tables = self::$XML->find("table");
        foreach ( $tables as $value ) {
            $sql = "CREATE TABLE IF NOT EXISTS `{$value->name}`(";
            $pk = $value->find("pk", 0);
            if ( $pk ) {
                $sql .= "`{$pk->name}` {$pk->type} unsigned NOT NULL ";
                if ( $pk->ai ) {
                    $sql .= "AUTO_INCREMENT ";
                }
                $sql .= "COMMENT '主键',";
            }
            //添加字段
            $fields = $value->find("fields", 0);
            foreach( $fields->children() as $fd ) {
                $sql .= "`{$fd->name}` {$fd->type} NOT NULL DEFAULT '{$fd->default}' COMMENT '{$fd->comment}',";
            }
            if ( $pk ) $sql .= "PRIMARY KEY (`{$pk->name}`)";
            $sql .= ") ENGINE={$value->engine}  DEFAULT CHARSET={$configs['charset']} ROW_FORMAT=FIXED COMMENT='{$value->comment}' AUTO_INCREMENT=1 ;";

            if ( self::query($sql) !== false ) {
                tprintOk("创建数据表 '{$value->name}' 成功！");
            } else {
                tprintError("创建数据表 '{$value->name}' 失败！");
            }

        }

    }

}
