<?php

namespace gmodel\utils;

/**
 * 创建dao文件。
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use gmodel\GModel;
use herosphp\utils\FileUtils;

require_once "StringBuffer.class.php";

class DaoFactory {

    /**
     * 生成Dao文件
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
            $interfaceName = "I".ucfirst(GModel::underline2hump($tableName))."Dao";
            //生成接口文件
            $daoInterface = $module."dao/interfaces/{$interfaceName}.class.php";
            if ( file_exists($daoInterface) ) { //若文件已经存在则跳过
                tprintWarning(" DAO接口文件 '{$daoInterface}' 已经存在，略过.");
                continue;
            }
            $sb = new StringBuffer();
            $sb->appendLine('<?php');
            $sb->appendLine("namespace {$configs["module"]}\\dao\\interfaces;");
            $sb->appendLine('use common\dao\interfaces\ICommonDao;');
            $sb->appendLine('/**');
            $sb->appendLine(" * {$configs["module"]}(DAO)接口");
            $sb->appendLine(" * @package {$configs["module"]}\\dao\\interfaces");
            $sb->appendLine(" * @author {$configs["author"]}<{$configs["email"]}>");
            $sb->appendLine(' */');
            $sb->appendLine("interface {$interfaceName} extends ICommonDao {}");

            if ( file_put_contents($daoInterface, $sb->toString()) !== false ) {
                tprintOk("生成DAO接口 '{$daoInterface}' 成功！");
            } else {
                tprintError("生成DAO接口 '{$daoInterface}' 失败！");
            }

            //生成实现dao
            $className = ucfirst(GModel::underline2hump($tableName))."Dao";
            $daoImpl = $module."dao/{$className}.class.php";
            if ( file_exists($daoImpl) ) { //若文件已经存在则跳过
                tprintWarning(" DAO文件 '{$daoImpl}' 已经存在，略过.");
                continue;
            }
            $sb = new StringBuffer();
            $sb->appendLine('<?php');
            $sb->appendLine("namespace {$configs["module"]}\\dao;");
            $sb->appendLine("");
            $sb->appendLine("use {$configs["module"]}\\dao\\interfaces\\{$interfaceName};");
            $sb->appendLine("use common\\dao\\CommonDao;");
            $sb->appendLine("use herosphp\\core\\Loader;");
            $sb->appendLine("");
            $sb->appendLine("Loader::import('{$configs["module"]}.dao.interfaces.{$interfaceName}');");
            $sb->appendLine("");
            $sb->appendLine('/**');
            $sb->appendLine(" * {$configs["module"]}(DAO)接口实现");
            $sb->appendLine(" * @package {$configs["module"]}\\dao");
            $sb->appendLine(" * @author {$configs["author"]}<{$configs["email"]}>");
            $sb->appendLine(' */');
            $sb->appendLine("class {$className} extends CommonDao implements {$interfaceName} {");

            if ( $assoc ) {

                $assoc = explode(",", $assoc);
                $assoc[] = $tableName;
                for ( $i = 0; $i < count($assoc)-1; $i++ ) {
                    $sb->appendLine("");
                    $dao = GModel::underline2hump($assoc[$i])."Dao";
                    $sb->appendTab("/**", 1);
                    $sb->appendTab(" * @var \\herosphp\\model\\C_Model", 1);
                    $sb->appendTab(" */", 1);
                    $sb->appendTab("private \${$dao} = null;", 1);
                }

                $sb->appendLine("");
                $sb->appendTab("/**", 1);
                $parameters = array();
                foreach ( $assoc as $m ) {
                    $m = "\$".GModel::underline2hump($m)."Model";
                    $sb->appendTab(" * @param {$m}", 1);
                    $parameters[] = $m;
                }
                $sb->appendTab(" */", 1);
                $sb->appendTab("public function __construct(".implode(", ", $parameters).") {", 1);
                for ( $i = 0; $i < count($assoc)-1; $i++ ) {
                    $m = GModel::underline2hump($assoc[$i])."Dao";
                    $sb->appendTab("\$this->{$m} = Loader::model({$parameters[$i]});", 2);
                }
                $sb->appendTab("\$this->setModelDao(Loader::model(".end($parameters)."));", 2);
                $sb->appendTab("}", 1);

            }
            $sb->appendLine("}");

            if ( file_put_contents($daoImpl, $sb->toString()) !== false ) {
                tprintOk("生成DAO '{$daoImpl}' 成功！");
            } else {
                tprintError("生成DAO '{$daoImpl}' 失败！");
            }

        }

    }

}
