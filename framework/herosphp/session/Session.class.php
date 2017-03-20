<?php
/*---------------------------------------------------------------------
 * session工厂
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @since 2015.02.20
 *-----------------------------------------------------------------------*/
namespace herosphp\session;

use herosphp\cache\RedisCache;
use herosphp\core\Loader;
Loader::import('session.FileSession', IMPORT_FRAME);
Loader::import('session.MemSession', IMPORT_FRAME);
Loader::import('session.RedisSession', IMPORT_FRAME);
class Session {

    /**
     * 开启session
     */
    public static function start() {

        //如果已经开启了SESSION则直接返回
        if ( isset($_SESSION) ) return true;

        //loading session configures
        $configs = Loader::config('session');
        $session_configs = $configs[$configs['session_handler']];
        switch ( $configs['session_handler'] ) {

            case 'file':
                FileSession::start($session_configs);
                break;

            case 'memo':
                MemSession::start($session_configs);
                break;

            case 'redis':
                RedisSession::start($session_configs);
                break;
        }

    }

    /**
     * 强制进行GC
     */
    public static function gc() {

        //loading session configures
        $configs = Loader::config('session');
        $session_configs = $configs[$configs['session_handler']];
        switch ( $configs['session_handler'] ) {

            case 'file':
                FileSession::gc($session_configs);
                break;

            case 'memo':
                MemSession::gc();
                break;

            case 'redis':
                RedisSession::gc();
                break;
        }

    }
	
}