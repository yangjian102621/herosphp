<?php

namespace gmodel\utils;

/**
 * 生成控制器文件
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use herosphp\files\FileUtils;

class ControllerFactory {

    /**
     * @param simple_html_dom $xml
     */
    public static function create($xml) {

        $moduleDir = APP_PATH."modules/";
        if ( !is_writable(dirname($moduleDir)) ) {
            tprintError("directory '{$moduleDir}' is not writeable， please add permissions.");
            return;
        }

        $root = $xml->find("root", 1);
        $configs = array(
            //"module" => $root->getAttribute("module"),
            "module" => 'admin',
            "author" => $root->getAttribute("author"),
            "email" => $root->getAttribute("email")
        );

        //创建目录
        $module = $moduleDir.$configs["module"]."/";
        FileUtils::makeFileDirs($module."action");
        FileUtils::makeFileDirs($module."template/default");

        $tables = $root->find("table");
        $tempContent = file_get_contents(dirname(__DIR__)."/template/controller.tpl");

        foreach ( $tables as $value ) {

            $tableName = $value->name;  //表名称
            $actionName = $value->getAttribute("action-name");
            if ( !$actionName ) continue;

            $className = ucfirst($actionName)."Action";
            $actionFile = $module."action/{$className}.class.php";
            if ( file_exists($actionFile) ) { //若文件已经存在则跳过
                tprintWarning("Warnning : DAO interface file '{$actionFile}' is existed， skiped.");
                continue;
            }
            $content = str_replace("{module}", $configs["module"], $tempContent);
            $content = str_replace("{author}", $configs["author"], $content);
            $content = str_replace("{email}", $configs["email"], $content);
            $content = str_replace("{table_name}", $tableName, $content);
            $content = str_replace("{class_name}", $className, $content);

            if ( file_put_contents($actionFile, $content) !== false ) {
                tprintOk("create Controller file '{$actionFile}' successfully！");
            } else {
                tprintError("Error : create Controller file '{$actionFile}' faild.");
            }

        }

    }

}
