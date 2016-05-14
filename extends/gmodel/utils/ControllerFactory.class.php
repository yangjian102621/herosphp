<?php

namespace gmodel\utils;

/**
 * 生成控制器文件
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use gmodel\GModel;
use herosphp\utils\FileUtils;

require_once "StringBuffer.class.php";

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
            "module" => $root->getAttribute("module"),
            "author" => $root->getAttribute("author"),
            "email" => $root->getAttribute("email")
        );

        //创建目录
        $module = $moduleDir.$configs["module"]."/";
        FileUtils::makeFileDirs($module."action");
        FileUtils::makeFileDirs($module."template/default");

        $tables = $root->find("table");

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
            $sb = new StringBuffer();
            $sb->appendLine('<?php');
            $sb->appendLine("namespace {$configs["module"]}\\action;");
            $sb->appendLine("");
            $sb->appendLine('use common\action\CommonAction;');
            $sb->appendLine('/**');
            $sb->appendLine(" * {$tableName} action");
            $sb->appendLine(" * @package {$configs["module"]}\\action");
            $sb->appendLine(" * @author {$configs["author"]}<{$configs["email"]}>");
            $sb->appendLine(' */');
            $sb->appendLine("class {$className} extends CommonAction {}");

            if ( file_put_contents($actionFile, $sb->toString()) !== false ) {
                tprintOk("create Controller file '{$actionFile}' successfully！");
            } else {
                tprintError("Error : create Controller file '{$actionFile}' faild.");
            }

        }

    }

}
