<?php

namespace gmodel\utils;

/**
 * 创建service文件。
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use gmodel\GModel;
use herosphp\utils\FileUtils;

require_once "StringBuffer.class.php";

class ServiceFactory {

    /**
     * 生成service文件
     * @param simple_html_dom $xml
     */
    public static function create($xml) {

        $moduleDir = APP_PATH."modules/";
        if ( !is_writable(dirname($moduleDir)) ) {
            tprintError("目录 '{$moduleDir}' 不可写， 请添加相应的权限!");
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
        FileUtils::makeFileDirs($module."dao/interfaces");
        FileUtils::makeFileDirs($module."service/interfaces");
        FileUtils::makeFileDirs($module."template/default");

        $tables = $root->find("table");

        foreach ( $tables as $value ) {

            $tableName = $value->name;  //表名称
            $assoc = $value->assoc;  //获取关联表信息
            $interfaceName = "I".ucfirst(GModel::underline2hump($tableName))."Service";
            //生成接口文件
            $serviceInterface = $module."service/interfaces/{$interfaceName}.class.php";
            if ( file_exists($serviceInterface) ) { //若文件已经存在则跳过
                tprintWarning(" Service接口文件 '{$serviceInterface}' 已经存在，略过.");
                continue;
            }
            $sb = new StringBuffer();
            $sb->appendLine('<?php');
            $sb->appendLine("namespace {$configs["module"]}\\service\\interfaces;");
            $sb->appendLine('use common\service\interfaces\ICommonService;');
            $sb->appendLine('/**');
            $sb->appendLine(" * {$configs["module"]}服务接口");
            $sb->appendLine(" * @package {$configs["module"]}\\service\\interfaces");
            $sb->appendLine(" * @author {$configs["author"]}<{$configs["email"]}>");
            $sb->appendLine(' */');
            $sb->appendLine("interface {$interfaceName} extends ICommonService{}");

            if ( file_put_contents($serviceInterface, $sb->toString()) !== false ) {
                tprintOk("生成Service接口 '{$serviceInterface}' 成功！");
            } else {
                tprintError("生成Service接口 '{$serviceInterface}' 失败！");
            }

            //生成实现dao
            $className = ucfirst(GModel::underline2hump($tableName))."Service";
            $serviceImpl = $module."service/{$className}.class.php";
            if ( file_exists($serviceImpl) ) { //若文件已经存在则跳过
                tprintWarning(" Service文件 '{$serviceImpl}' 已经存在，略过.");
                continue;
            }
            $sb = new StringBuffer();
            $sb->appendLine('<?php');
            $sb->appendLine("namespace {$configs["module"]}\\service;");
            $sb->appendLine("");
            $sb->appendLine("use {$configs["module"]}\\service\\interfaces\\{$interfaceName};");
            $sb->appendLine("use common\\service\\CommonService;");
            $sb->appendLine("use herosphp\\core\\Loader;");
            $sb->appendLine("");
            $sb->appendLine("Loader::import('{$configs["module"]}.service.interfaces.{$interfaceName}');");
            $sb->appendLine("");
            $sb->appendLine('/**');
            $sb->appendLine(" * {$configs["module"]}(Service)接口实现");
            $sb->appendLine(" * @package {$configs["module"]}\\service");
            $sb->appendLine(" * @author {$configs["author"]}<{$configs["email"]}>");
            $sb->appendLine(' */');
            $sb->appendLine("class {$className} extends CommonService implements {$interfaceName} {}");

            if ( file_put_contents($serviceImpl, $sb->toString()) !== false ) {
                tprintOk("生成DAO '{$serviceImpl}' 成功！");
            } else {
                tprintError("生成DAO '{$serviceImpl}' 失败！");
            }

        }

    }

}
