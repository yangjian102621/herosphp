<?php
/*---------------------------------------------------------------------
 * session工厂
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 * @since 2015.02.20
 *-----------------------------------------------------------------------*/
namespace herosphp\session;

use herosphp\core\Loader;
Loader::import('session.FileSession', IMPORT_FRAME);
Loader::import('session.MemSession', IMPORT_FRAME);
class Session {

    /**
     * 开启session
     */
    public static function start() {

        //如果已经开启了SESSION则直接返回
        if ( isset($_SESSION) ) return true;

        //loading session configures
        $configs = Loader::config('session','session');
        switch ( SESSION_HANDLER ) {

            case 'file':
                FileSession::start($configs[SESSION_HANDLER]);
                break;

            case 'memo':
                MemSession::start($configs[SESSION_HANDLER]);
                break;
        }

    }
	
}