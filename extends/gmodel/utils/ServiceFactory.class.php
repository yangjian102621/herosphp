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
     * 创建 service 文件
     * @param $options
     */
    public static function create($options) {

        if ( !isset($options['module']) ) return tprintError("Error : --module is needed.");
        if ( !isset($options['service']) ) return tprintError("Error : service name is needed.");
        if ( !isset($options['author']) ) $options['author'] = 'yangjian';
        if ( !isset($options['email']) ) $options['email'] = 'yangjian102621@gmail.com';
        if ( !isset($options['date']) ) $options['date'] = date('Y-m-d');
        if ( !isset($options['desc']) ) $options['desc'] = $options['service'];
        if ( !isset($options['extend']) ) $options['extend'] = 'common\\service\\CommonService';

        $moduleDir = APP_PATH."modules/{$options['module']}/";
        if ( !is_writable(dirname($moduleDir)) ) {
            tprintError("directory '{$moduleDir}' is not writeable， please add permissions.");
            return;
        }
        //创建目录
        if ( !file_exists($moduleDir.'service') ) {
            FileUtils::makeFileDirs($moduleDir.'service');
        }

        $className = ucfirst($options['service'])."Service";
        $replacements = array(
            '{module}' => $options['module'],
            '{desc}' => $options['desc'],
            '{author}' => $options['author'],
            '{email}' => $options['email'],
            '{date}' => $options['date'],
            '{className}' => $className,
        );
        if ( $options['extend'] ) { //继承了某个类
            $replacements['{CommonService}'] = 'extends CommonService ';
            $replacements['{common_name_space}'] = 'use common\\service\\CommonService;';
        } else {
            $replacements["{CommonService}"] = '';
            $replacements["{common_name_space}\n"] = '';
        }

        $filename = $moduleDir.'service/'.$className.'.class.php';
        if ( file_exists($filename) ) { //若文件已经存在则跳过
            return tprintWarning("Warnning : Service file '{$filename}' is existed， skiped.");
        }

        $tempContent = file_get_contents(dirname(__DIR__)."/template/service.tpl");
        $content = str_replace(array_keys($replacements), $replacements, $tempContent);

        if ( file_put_contents($filename, $content) ) {
            tprintOk("Create Service '{$options['service']}' successfully！");
        } else {
            tprintError("Error : Create Service '{$options['service']}' faild.");
        }

        //更新service beans 文件
        $buffer = new StringBuffer();
        $buffer->appendLine("'demo.{$options['service']}.service' => array(");
        $buffer->appendTab("'@type' => Beans::BEAN_OBJECT,", 2);
        $buffer->appendTab("'@class' => '{$options['module']}\\service\\".$options['service']."',", 2);
        $buffer->appendTab("'@params' => array('User')", 2);
        $buffer->appendTab("),", 1);
        $buffer->appendTab("//{beansTag}", 1);

        $beansFile = APP_PATH."configs/beans/beans.{$options['module']}.config.php";
        if ( file_exists($beansFile) ) {
            $content = file_get_contents($beansFile);

        } else {
            $content = file_get_contents(dirname(__DIR__)."/template/bean.config.tpl");
        }

        $content = str_replace("//{beansTag}", $buffer->toString(), $content);
        if ( file_put_contents($beansFile, $content) ) {
            tprintOk("Update beans config file '{$beansFile}' successfully！");
        } else {
            tprintError("Error : Update beans config file '{$beansFile}' faild.");
        }
    }

}
