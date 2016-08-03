<?php

namespace gmodel\utils;

/**
 * 数据库，数据表。
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
class DBFactory {

    private static $debug = true;   //是否开启调试模式

    /**
     * @var MySQLi
     */
    private static $DB_CONN = null; //数据库连接

    private static $DEFAULT_VALUE_KEYWORD = array(
        "CURRENT_TIMESTAMP", "NULL"
    );

    /**
     * @var simple_html_dom|null
     */
    private static $XML = null;

    /**
     * @param simple_html_dom $xml
     * 创建数据库结构
     */
    public static function create($xml) {

        self::$XML = $xml;
        $root = self::$XML->find("root", 1);
        $configs = array(
            "dbhost" => $root->dbhost,
            "dbuser" => $root->getAttribute("dbuser"),
            "dbpass" => $root->getAttribute("dbpass"),
            "dbname" => $root->getAttribute("dbname"),
            "charset" => $root->getAttribute("charset"),
            "table-prefix" => $root->getAttribute("table-prefix")
        );

        self::$DB_CONN = mysqli_connect($configs["dbhost"], $configs["dbuser"], $configs["dbpass"]);
        if ( !self::$DB_CONN ) {
            tprintError("Error : can not to connect to the database.");
            return;
        }
        self::$DB_CONN->query("SET names {$configs["charset"]}");
        self::$DB_CONN->query("SET character_set_client = {$configs["charset"]}");
        self::$DB_CONN->query("SET character_set_results = {$configs["charset"]}");

        $sql = "CREATE DATABASE IF NOT EXISTS `{$configs["dbname"]}` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;";
        if ( self::query($sql) !== false ) {
            self::query("USE `{$configs["dbname"]}`;");
            tprintOk("create database successfully.");
        } else {
            tprintError("Error : creeat database faild.");
        }

        //生成数据库配置文档
        $dbConfigFile = APP_PATH."configs/db.config.php";
        $tempContent = file_get_contents(dirname(__DIR__)."/template/db.config.tpl");
        if ( $tempContent != "" ) {
            $content = str_replace("{db_host}", $configs["dbhost"], $tempContent);
            $content = str_replace("{db_user}", $configs["dbuser"], $content);
            $content = str_replace("{db_name}", $configs["dbname"], $content);
            $content = str_replace("{db_pass}", $configs["dbpass"], $content);
            $content = str_replace("{db_charset}", $configs["charset"], $content);
            $content = str_replace("{table_prefix}", $configs["table-prefix"], $content);

            if ( file_put_contents($dbConfigFile, $content) !== false ) {
                tprintOk("create db config file '{$dbConfigFile}' successfully！");
            } else {
                tprintError("create db config file '{$dbConfigFile}' faild!");
            }
        }

        self::createTables($configs);

    }

    //执行查询
    private static function query($sql) {

        if ( self::$debug ) tprintWarning($sql);

        return self::$DB_CONN->query($sql);
    }

    /**
     * 创建表结构
     * @param $configs
     */
    private static function createTables($configs) {

        $tables = self::$XML->find("table");
        foreach ( $tables as $value ) {
            $tableName = $configs["table-prefix"].$value->name;
            self::query("DROP TABLE IF EXISTS `{$tableName}`");
            $sql = "CREATE TABLE `{$tableName}`(";
            $pk = $value->find("pk", 0);
            if ( $pk ) {
                $sql .= "`{$pk->name}` {$pk->type} NOT NULL ";
                if ( $pk->ai ) {
                    $sql .= "AUTO_INCREMENT ";
                }
                $sql .= "COMMENT '主键',";
            }
            //添加字段
            $fields = $value->find("fields", 0);
            foreach( $fields->children() as $fd ) {
                if ( $fd->default || $fd->default === "0" ) {   //has default value
                    if ( in_array($fd->default, self::$DEFAULT_VALUE_KEYWORD) ) {
                        $sql .= "`{$fd->name}` {$fd->type} NOT NULL DEFAULT {$fd->default} COMMENT '{$fd->comment}',";
                    } else {
                        $sql .= "`{$fd->name}` {$fd->type} NOT NULL DEFAULT '{$fd->default}' COMMENT '{$fd->comment}',";
                    }
                } else { //has not default value
                    $sql .= "`{$fd->name}` {$fd->type} NOT NULL COMMENT '{$fd->comment}',";
                }

                //创建索引
                if ( $fd->getAttribute("add-index") == "true" ) {
                    $indexType = $fd->getAttribute("index-type");
                    if ( $indexType == "normal" ) {
                        $sql .= "KEY `{$fd->name}` (`{$fd->name}`), ";
                    } elseif ( $indexType == "unique" ) {
                        $sql .= "UNIQUE KEY `{$fd->name}` (`{$fd->name}`),";
                    }
                }
            }
            if ( $pk ) $sql .= "PRIMARY KEY (`{$pk->name}`)";
            $sql .= ") ENGINE={$value->engine}  DEFAULT CHARSET={$configs['charset']} COMMENT='{$value->comment}' AUTO_INCREMENT=1 ;";

            if ( self::query($sql) !== false ) {
                tprintOk("create table '{$tableName}' successfully.");

            } else {
                tprintError("create table '{$tableName}' faild.");
                tprintError(self::$DB_CONN->error);
            }

        }

    }

}
