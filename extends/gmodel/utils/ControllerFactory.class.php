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
     * 创建 controller 文件
     * @param $options
     */
    public static function create($options) {

        if ( !isset($options['module']) ) return tprintError("Error : --module is needed.");
        if ( !isset($options['controller']) ) return tprintError("Error : controller name is needed.");
        if ( !isset($options['author']) ) $options['author'] = 'yangjian';
        if ( !isset($options['email']) ) $options['email'] = 'yangjian102621@gmail.com';
        if ( !isset($options['date']) ) $options['date'] = date('Y-m-d');
        if ( !isset($options['desc']) ) $options['desc'] = $options['controller'];

        $moduleDir = APP_PATH."modules/{$options['module']}/";
        if ( !is_writable(dirname($moduleDir)) ) {
            tprintError("directory '{$moduleDir}' is not writeable， please add permissions.");
            return;
        }
        //创建目录
        if ( !file_exists($moduleDir.'action') ) {
            FileUtils::makeFileDirs($moduleDir.'action');
        }

        $replacements = array(
            '{module}' => $options['module'],
            '{desc}' => $options['desc'],
            '{author}' => $options['author'],
            '{email}' => $options['email'],
            '{date}' => $options['date'],
            '{className}' => ucfirst($options['controller'])."Action",
            '{serviceBean}' => 'protected $serviceBean = "'.$options['module'].'.'.$options['controller'].'.service";',
        );
        if ( $options['module'] == 'admin' ) { //后台的控制器
            $commonAction = $moduleDir."action/CommonAction.class.php";
            if ( !file_exists($commonAction) ) {
                copy(dirname(__DIR__)."codetpl/CommonAction.class.php", $moduleDir."action/CommonAction.class.php");
            } else {
                printLine("Basic action exsits.");
            }
            $tempFile = dirname(__DIR__)."/template/admin-controller.tpl";
        } else {
            $tempFile = dirname(__DIR__)."/template/controller.tpl";
        }

        $className = ucfirst($options['controller'])."Action";
        $actionFile = $moduleDir."action/{$className}.class.php";
        if ( file_exists($actionFile) ) { //若文件已经存在则跳过
            return tprintWarning("Warnning : controller file '{$actionFile}' is existed， skiped.");
        }

        $tempContent = file_get_contents($tempFile);
        $content = str_replace(array_keys($replacements), $replacements, $tempContent);

        if ( file_put_contents($actionFile, $content) ) {
            tprintOk("Create Controller '{$options['controller']}' successfully！");
        } else {
            tprintError("Error : Create Controller '{$options['controller']}' faild.");
        }
    }


}
