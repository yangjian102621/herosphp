<?php

namespace gmodel\utils;

/**
 * 生成控制器文件
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use herosphp\files\FileUtils;
use herosphp\string\StringBuffer;

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
            //"module" => $root->getAttribute("author"),
            "author" => $root->getAttribute("author"),
            "email" => $root->getAttribute("email")
        );

        //创建目录
        $module = $moduleDir."/admin/";
        FileUtils::makeFileDirs($module."action");
        FileUtils::makeFileDirs($module."template/default");

        $tables = $root->find("table");
        $tempContent = file_get_contents(dirname(__DIR__)."/template/controller.tpl");
        $jsTempContent = file_get_contents(dirname(__DIR__)."/template/appjs.tpl");
        $appjsConfigCode = new StringBuffer();
        $appendCodeSymbol = '"common": "{app}/admin/common.js",';   //追加js的位置
        $appjsConfigCode->appendLine($appendCodeSymbol);

        foreach ( $tables as $value ) {

            $tableName = $value->name;  //表名称
            $actionName = $value->getAttribute("action-name");
            if ( !$actionName ) continue;

            $className = ucfirst($actionName)."Action";
            $actionFile = $module."action/{$className}.class.php";
            if ( file_exists($actionFile) ) { //若文件已经存在则跳过
                tprintWarning("Warnning : controller file '{$actionFile}' is existed， skiped.");
                continue;
            }
            $content = str_replace("{module}", $configs["module"], $tempContent);
            $content = str_replace("{author}", $configs["author"], $content);
            $content = str_replace("{email}", $configs["email"], $content);
            $content = str_replace("{table_name}", $tableName, $content);
            $content = str_replace("{class_name}", $className, $content);

            //注入service bean
            $serviceBean = "{$configs["module"]}.{$actionName}.service";
            $content = str_replace('{service_bean}', $serviceBean, $content);

            if ( file_put_contents($actionFile, $content) !== false ) {
                tprintOk("create Controller file '{$actionFile}' successfully！");
                $appjsConfigCode->appendTab('"'.$actionName.'": "{app}/admin/'.$actionName.'.js",', 3);
            } else {
                tprintError("Error : create Controller file '{$actionFile}' faild.");
            }

            //创建js文件
            $jsfile = APP_ROOT."res/js/app/admin/{$actionName}.js";
            $jsTempContent = str_replace('{action_name}', $actionName, $jsTempContent);
            if ( !file_exists($jsfile) && file_put_contents($jsfile, $jsTempContent) !== false ) {
                tprintOk("create js file '{$jsfile}' successfully！");

            } else {
                tprintError("Error : create js file '{$jsfile}' faild.");
            }


        } //end foreach

        //替换js config 文件
        $jsConfigFile = APP_ROOT."res/js/config.js";
        $jscode = file_get_contents($jsConfigFile);
        $jscode = str_replace($appendCodeSymbol, $appjsConfigCode->toString(), $jscode);
        if ( file_put_contents($jsConfigFile, $jscode) !== false ) {
            tprintOk("update js config file '{$jsConfigFile}' successfully！");
        } else {
            tprintError("Error : update js config file '{$jsConfigFile}' faild.");
        }
    }

}
