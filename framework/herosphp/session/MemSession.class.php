<?php
/*---------------------------------------------------------------------
 * session handler for memcache. save session data to memcache by user
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
use herosphp\session\interfaces\ISession;

Loader::import('session.interfaces.ISession', IMPORT_FRAME);
class MemSession implements  ISession {

    /**
     * handler for memcache server
     * @var Object
     */
    private static $handler = NULL;

    /**
     * @var array 配置信息
     */
    private static $config;

	/**
	 * @see	\herosphp\session\interfaces\ISession::start().
	 */
	public static function start( $config = NULL ) {

        if ( !$config ) {
            if ( APP_DEBUG ) {
                E("config should be pass to app");
            }
        }

        self::$handler = new \Memcache();
        self::$handler->connect($config['host'], $config['port']) or E("could not to connect the memcache server!");
        self::$config = $config;
        if ( !$config['gc_maxlifetime'] ) {
            self::$config['gc_maxlifetime'] = ini_get('session.gc_maxlifetime');
        }

		session_set_save_handler(
			array(__CLASS__,'open'),
			array(__CLASS__,'close'),
			array(__CLASS__,'read'),
			array(__CLASS__,'write'),
			array(__CLASS__,'destroy'),
			array(__CLASS__,'gc')
		);
		session_start();
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::open().
	 */
	public static function open( $savePath, $sessionName ) {
		//do nothing here.
		return TRUE;
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::close().
	 */
	public static function close() {
		//do nothing here
		return TRUE;
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::read().
	 */
	public static function read( $sessionId ) {

		if ( self::$handler  == NULL ) return '';
		$data = self::$handler->get( $sessionId );
		if ( !$data ) return '';
		return $data;

	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::write().
	 */
	public static function write( $sessionId, $data ) {

		$method = $data ? 'set' : 'replace';
		return self::$handler->$method( $sessionId, $data,MEMCACHE_COMPRESSED, self::$config['gc_maxlifetime'] );
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::destroy().
	 */
	public static function destroy( $sessionId ) {
		return self::$handler->delete( $sessionId );
	}

	/**
	 * @see	\herosphp\session\interfaces\ISession::gc().
	 */
	public static function gc( $maxLifeTime ) {
		//do nothing here.
		return TRUE;
	}

}
?>
