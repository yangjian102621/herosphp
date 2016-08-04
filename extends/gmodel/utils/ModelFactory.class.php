<?php

namespace gmodel\utils;

/**
 * 创建model文件。
 * @author yangjian<yangjian102621@gmail.com>
 *
 */
use gmodel\GModel;
use herosphp\files\FileUtils;

class ModelFactory {

    /**
     * 创建数据模型文件，生成beans配置文件
     * @param simple_html_dom $xml
     */
    public static function create($xml) {

        $modelDir = APP_PATH."configs/models/";
        if ( !is_writable(dirname($modelDir)) ) {
            tprintError("Error: directory '{$modelDir}' is not writeadble， please add permissions.");
            return;
        }

        //create directory
        FileUtils::makeFileDirs($modelDir);

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
                tprintWarning("Warnning : model file '{$modelFile}' has existed，skiped.");
                continue;
            }
            $pk = $value->find("pk", 0);

            $content = str_replace("{table_name}", $value->name, $tempContent);
            if ( $pk ) {
                $content = str_replace("{pk}", $pk->name, $content);
            }
            $content = str_replace("{model_name}", ucfirst(GModel::underline2hump($value->name))."Model", $content);
            $content = str_replace("{app_name}", APP_NAME, $content);
            $content = str_replace("{author}", $configs["author"], $content);
            $content = str_replace("{email}", $configs["email"], $content);

            if ( file_put_contents($modelFile, $content) !== false ) {
                tprintOk("create model file '{$modelFile}' successfully.");
            } else {
                tprintError("Error: create model file '{$modelFile}' faild.");
            }

        }

    }

}
