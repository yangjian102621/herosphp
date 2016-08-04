<?php

namespace gmodel\utils;

/**
 * 创建service文件。
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use gmodel\GModel;
use herosphp\files\FileUtils;
use herosphp\string\StringBuffer;

class ServiceFactory {

    /**
     * 生成service文件
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
        FileUtils::makeFileDirs($module."service/interfaces");

        $serviceConfigs = $root->find("service-config service");

        foreach ( $serviceConfigs as $value ) {

            $className = $value->name;  //class name of service
            $interfaceName = "I".$className;
            //生成接口文件
            $serviceInterface = $module."service/interfaces/{$interfaceName}.class.php";
            if ( file_exists($serviceInterface) ) { //若文件已经存在则跳过
                tprintWarning("Warnning : The service interface file '{$serviceInterface}' is existed, skiped.");
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
                tprintOk("create service interface file '{$serviceInterface}' successfully.");
            } else {
                tprintError("Error : create service interface file '{$serviceInterface}' faild.");
            }

            //生成实现dao
            $serviceImpl = $module."service/{$className}.class.php";
            if ( file_exists($serviceImpl) ) { //若文件已经存在则跳过
                tprintWarning("Warnning : The service file '{$serviceImpl}' is existed, skiped.");
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
                tprintOk("create Service file '{$serviceImpl}' succcessfully.");
            } else {
                tprintError("Error : create Service file '{$serviceImpl}' faild.");
            }

        }


        //生成beans配置文件
        $beansDir = APP_PATH."configs/beans/";
        $beansFile = $beansDir."beans.{$root->module}.config.php";
        FileUtils::makeFileDirs($beansDir); //create beans directory

        $sb = new StringBuffer();
        $sb->appendLine('<?php');
        $sb->appendLine('use herosphp\bean\Beans;');
        $sb->appendLine('/**');
        $sb->appendLine(" * {$configs["module"]}模块Beans装配配置");
        $sb->appendLine(" * @author {$configs["author"]}<{$configs["email"]}>");
        $sb->appendLine(' */');
        $sb->appendLine('$beans = array(');

        foreach ( $serviceConfigs as $value ) {

            $serviceClassName = $value->name;
            $daoClassName = $value->dao;
            $models = $value->model;  //get the association models
            if ( strpos($models, ",") !== false ) {
                $models = explode(",", $models);
                foreach ($models as $key => $value) { //转驼峰
                    $assoc[$key] = ucfirst(GModel::underline2hump($value));
                }
            }
            $sb->appendTab("//{$className} configs", 1);
            if ( is_array($models) ) {
                $sb->appendTab("'{$configs["module"]}.".GModel::underline2hump($models[0]).".service' => array(", 1);
            } else {
                $sb->appendTab("'{$configs["module"]}.".GModel::underline2hump($models).".service' => array(", 1);
            }

            $sb->appendTab("'@type' => Beans::BEAN_OBJECT,", 2);
            $sb->appendTab("'@class' => '{$configs["module"]}\\service\\{$serviceClassName}',", 2);
            $sb->appendTab("'@attributes' => array(", 2);
            $sb->appendTab("'@bean/modelDao'=>array(", 3);
            $sb->appendTab("'@type'=>Beans::BEAN_OBJECT,", 4);
            $sb->appendTab("'@class'=>'{$configs["module"]}\\dao\\{$daoClassName}',", 4);
            if ( is_array($models) ) {
                $sb->appendTab("'@params' => array('".implode("','", $assoc)."')", 4);
            } else {
                $sb->appendTab("'@params' => array('".ucfirst(GModel::underline2hump($models))."')", 4);
            }
            $sb->appendTab(")", 3);
            $sb->appendTab("),", 2);
            $sb->appendTab("),", 1);

        }

        $sb->appendLine(');');
        $sb->appendLine('return $beans;');

        if ( file_put_contents($beansFile, $sb->toString()) !== false ) {
            tprintOk("create Beans config file '{$beansFile}' successfully.");
        } else {
            tprintError("create Beans config file '{$beansFile}' faild.");
        }

    }

}
