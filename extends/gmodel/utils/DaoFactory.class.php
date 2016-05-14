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
        FileUtils::makeFileDirs($module."dao/interfaces");

        $serviceConfigs = $root->find("service-config service");  //get service configs
        foreach ( $serviceConfigs as $value ) {

            $className = $value->dao;  //class name of dao
            $models = $value->model;  //get the association models
            $interfaceName = "I".$className;
            //生成接口文件
            $daoInterface = $module."dao/interfaces/{$interfaceName}.class.php";
            if ( file_exists($daoInterface) ) { //若文件已经存在则跳过
                tprintWarning("Warnning : DAO interface file '{$daoInterface}' is existed， skiped.");
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
                tprintOk("create DAO interface file '{$daoInterface}' successfully！");
            } else {
                tprintError("Error : create DAO interface file '{$daoInterface}' faild.");
            }

            //生成实现dao
            $daoImpl = $module."dao/{$className}.class.php";
            if ( file_exists($daoImpl) ) { //若文件已经存在则跳过
                tprintWarning("Warnning : DAO file '{$daoImpl}' is existed， skiped.");
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

            if ( strpos($models, ",") !== false ) {

                $models = explode(",", $models);
                foreach ( $models as $m ) {
                    $sb->appendLine("");
                    $dao = GModel::underline2hump($m)."Dao";
                    $sb->appendTab("/**", 1);
                    $sb->appendTab(" * @var \\herosphp\\model\\C_Model", 1);
                    $sb->appendTab(" */", 1);
                    $sb->appendTab("private \${$dao} = null;", 1);
                }

                $sb->appendLine("");
                $sb->appendTab("/**", 1);
                $parameters = array();
                foreach ( $models as $m ) {
                    $m = "\$".GModel::underline2hump($m)."Model";
                    $sb->appendTab(" * @param {$m}", 1);
                    $parameters[] = $m;
                }
                $sb->appendTab(" */", 1);
                $sb->appendTab("public function __construct(".implode(", ", $parameters).") {", 1);

                //init the modelDao
                $sb->appendTab("\$this->setModelDao(Loader::model(".array_shift($parameters)."));", 2);
                for ( $i = 0; $i < count($models); $i++ ) {
                    $m = GModel::underline2hump($models[$i])."Dao";
                    $sb->appendTab("\$this->{$m} = Loader::model({$parameters[$i]});", 2);
                }
                $sb->appendTab("}", 1);

            }
            $sb->appendLine("}");

            if ( file_put_contents($daoImpl, $sb->toString()) !== false ) {
                tprintOk("create DAO file '{$daoImpl}' successfully.");
            } else {
                tprintError("Error : create DAO file '{$daoImpl}' faild.");
            }

        }

    }

}
