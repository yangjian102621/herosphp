<?php

namespace gmodel\utils;

/**
 * 创建model文件。
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use gmodel\GModel;

require_once "StringBuffer.class.php";

class ModelFactory {

    /**
     * 创建数据模型文件，生成beans配置文件
     * @param simple_html_dom $xml
     */
    public static function create($xml) {

        $modelDir = APP_PATH."configs/models/";
        $beansDir = APP_PATH."configs/beans/";
        if ( !is_writable(dirname($modelDir)) ) {
            tprintError("目录 '{$modelDir}' 和 '{$beansDir}' 不可写， 请添加相应的权限!");
            return;
        }

        $root = $xml->find("root", 1);
        $configs = array(
            "module" => $root->getAttribute("module"),
            "author" => $root->getAttribute("author"),
            "email" => $root->getAttribute("email")
        );

        $tables = $root->find("table");
        $tempContent = file_get_contents(dirname(__DIR__)."/template/model.tpl");

        foreach ( $tables as $value ) {

            $modelFile = $modelDir.ucfirst(GModel::underline2hump($value->name)).".model.php";
            if ( file_exists($modelFile) ) { //若文件已经存在则跳过
                tprintWarning("model 文件 '{$modelFile}' 已经存在，略过.");
                continue;
            }

            $content = str_replace("{table_name}", $value->name, $tempContent);
            $content = str_replace("{app_name}", APP_NAME, $content);
            $content = str_replace("{author}", $configs["author"], $content);
            $content = str_replace("{email}", $configs["email"], $content);

            if ( file_put_contents($modelFile, $content) !== false ) {
                tprintOk("生成 model '{$modelFile}' 成功！");
            } else {
                tprintError("生成 model '{$modelFile}' 失败！");
            }

        }

        //生成beans配置文件
        $beansFile = $beansDir."beans.{$root->module}.config.php";
        if ( file_exists($beansFile) ) { //若文件已经存在则跳过
            tprintWarning("beans 文件 '{$beansFile}' 已经存在，略过.");
            return;
        }
        $sb = new StringBuffer();
        $sb->appendLine('<?php');
        $sb->appendLine('use herosphp\bean\Beans;');
        $sb->appendLine('/**');
        $sb->appendLine(" * {$configs["module"]}模块Beans装配配置");
        $sb->appendLine(" * @author {$configs["author"]}<{$configs["email"]}>");
        $sb->appendLine(' */');
        $sb->appendLine('$beans = array(');
        foreach ( $tables as $value ) {

            $tableName = $value->name;
            $assoc = $value->assoc;  //获取关联表信息
            $sb->appendTab("//{$tableName}服务", 1);
            $sb->appendTab("'{$configs["module"]}.{$tableName}.service' => array(", 1);
            $sb->appendTab("'@type' => Beans::BEAN_OBJECT,", 2);
            $sb->appendTab("'@class' => '{$configs["module"]}\\service\\".ucfirst(GModel::underline2hump($tableName))."Service',", 2);
            $sb->appendTab("'@attributes' => array(", 2);
            $sb->appendTab("'@bean/modelDao'=>array(", 3);
            $sb->appendTab("'@type'=>Beans::BEAN_OBJECT,", 4);
            $sb->appendTab("'@class'=>'{$configs["module"]}\\dao\\".ucfirst(GModel::underline2hump($tableName))."Dao',", 4);
            if ( $assoc ) {
                $assoc = explode(",", $assoc);
                $assoc[] = $tableName;
                foreach ($assoc as $key => $value) { //转驼峰
                    $assoc[$key] = ucfirst(GModel::underline2hump($value));
                }
                $sb->appendTab("'@params' => array('".implode("','", $assoc)."')", 4);
            } else {
                $sb->appendTab("'@params' => array('".ucfirst(GModel::underline2hump($tableName))."')", 4);
            }
            $sb->appendTab(")", 3);
            $sb->appendTab("),", 2);
            $sb->appendTab("),", 1);

        }

        $sb->appendLine(');');
        $sb->appendLine('return $beans;');

        if ( file_put_contents($beansFile, $sb->toString()) !== false ) {
            tprintOk("生成 model '{$modelFile}' 成功！");
        } else {
            tprintError("生成 model '{$modelFile}' 失败！");
        }

    }

}
