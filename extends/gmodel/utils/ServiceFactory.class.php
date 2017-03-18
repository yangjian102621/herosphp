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
        if ( !isset($options['interface']) ) return tprintError("Error : --table is needed.");
        if ( !isset($options['author']) ) $options['author'] = 'yangjian';
        if ( !isset($options['email']) ) $options['email'] = 'yangjian102621@gmail.com';
        if ( !isset($options['date']) ) $options['date'] = date('Y-m-d');

        $moduleDir = APP_PATH."modules/{$options['module']}/";
        if ( !is_writable(dirname($moduleDir)) ) {
            tprintError("directory '{$moduleDir}' is not writeable， please add permissions.");
            return;
        }
        //创建目录
        if ( !file_exists($moduleDir.'service') ) {
            FileUtils::makeFileDirs($moduleDir.'service');
        }

    }

}
