<?php
/*---------------------------------------------------------------------
 * artisan 工具类
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/
namespace herosphp;

use gmodel\GModel;
use herosphp\core\Loader;

Loader::import("extends.gmodel.GModel", IMPORT_CUSTOM);

class Artisan {

    private static $SHORT_OPS = 'hv';

    private static $LONG_OPTS = array(
        'make-model:'   => "Use to create Model. Optional value is model's name.",
        'table:'   => "Specify the table for Model.",
        'pk:'   => "Specify primary key of the table. Default value is 'id'",

        'make-controller:'   => "Use to create Controller. Optional value is Controller's name.",
        'make-service:'   => "Use to create Service. Optional value is Service's name.",
        'module:' => 'Specify the module of Model|Service|Controller.',
        'extend:' => "Specify the parent class of Service. default value is 'common\\service\\CommonService'",

        'make-db:'   => "Use to create a database.",
        'dbhost:'   => "Specify the database server host. Default value is 127.0.0.1",
        'dbuser:'   => "Specify the user for connections of database. Default value is root",
        'dbpass:'   => "Specify the password for database server user. Default value is 123456",
        'dbname:'   => "Specify the name of database.",
        'charset:'   => "Specify the charset of database. Default value is UTF-8",

        'make-table:'   => "Use to create database table. Optional value is tables configure xml file path.",

        'import:'   => 'Use to import database or table from sql file.',

        'author:'   => 'Specify the compontent file create author info. Default value is {yangjian}',
        'email:'    => 'Specify the compontent file create author email info. Default value is {yangjian102621@gmail.com}',
        'desc:'     => 'Specify the compontent file description info.',
        'date:'     => 'Specify the compontent file create date info. Default value is the day that file is created.',

        'help'      => 'Show the help info. Shortcut -h.',
        'version'   => 'Show the version info. Shortcut -v.'
    );

    public static function run() {

        $opts = getopt(self::$SHORT_OPS, array_keys(self::$LONG_OPTS));

        if ( empty($opts) || isset($opts['help']) || isset($opts['h']) ) {
            return self::printHelpInfo();
        }
        if ( isset($opts['version']) || isset($opts['v']) ) {
            return self::printVersion();
        }

        if ( $opts['make-db'] ) { //创建数库
            $opts['dbname'] = $opts['make-db'];
            return GModel::createDatabase($opts);
        }

        if ( $opts['make-table'] ) { //创建数据表
            $opts['xmlpath'] = $opts['make-table'];
            return GModel::createTables($opts);
        }

        if ( $opts['make-model'] ) { //创建模型
            if ( strpos($opts['make-model'], '.xml') ) {
                $opts['xmlpath'] = $opts['make-model'];
            } else {
                $opts['model'] = $opts['make-model'];
            }
            return GModel::createModel($opts);
        }

        if ( $opts['make-service'] ) { //创建服务
            $opts['service'] = $opts['make-service'];
            return GModel::createService($opts);
        }

        if ( $opts['make-controller'] ) {
            $opts['controller'] = $opts['make-controller'];
            return GModel::createController($opts);
        }

    }

    //打印帮助信息
    protected static function printHelpInfo() {
        tprintOk('Welcome to use HerosPHP artisan.');
        self::printVersion();
        tprintOk('Usage: ');
        printLine("  ./artisan [--make-model=Model | --make-service=Service | --make-controller=Controller | --import=sql] [options]");
        printLine();
        tprintOk('Options: ');
        foreach ( self::$LONG_OPTS as $key => $value ) {
            $key = rtrim($key, ":");
            printLine("  --{$key} {$value}");
            printLine();
        }
    }

    //打印版本信息
    protected static function printVersion() {
        printLine("Version : 1.0");
    }

}
