<?php
namespace herosphp\session\interfaces;

/*---------------------------------------------------------------------
 * interface for session handler
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 * @since 2015.02.20
 *-----------------------------------------------------------------------*/
 
interface ISession {

    /**
     * open and initialization session.
     *
     * @param array $config config for session
     * @return mixed
     */
    public static function start( $config = NULL );
	
	/**
	 * session pen handler function. it excuted when the session is being opened.
	 * excuted : session_start();
     *
	 * @param string $savePath	session save path, it used when the session handler is 'files'.
	 * @param string $sessionName   name of session, set by php.ini 'session.name'.
	 */
	public static function open( $savePath, $sessionName );
	
	/**
	 * close session.
	 * excuted : session_destroy(), session_write_close();
	 */
	public static function close();
	
	/**
	 * read session data to $_SESSION array.
	 * excuted : session_start(), $var = $_SESSION['aaa']
	 *
	 * @param string $sessionId		php session id
	 */
	public static function read( $sessionId );
	
	/**
	 * write data to session.
	 * excuted : session_write_close(); force to commit data to SESSION,  $_SESSION[]="aaa";
	 * 
	 * @param string $sessionId  php session id.
	 * @param string|array $data data to write to session.
	 */
	public static function write( $sessionId, $data );
	
	/**
	 * destroy the session.
	 * excuted : session_destroy();
	 * 
	 * @param		string		$sessionId			php session id.
	 */
	public static function destroy( $sessionId );
	
	/**
	 * session gc, determined by php.ini(session.gc_probability and session.gc_divisor)
	 * excuted : open(), read(), session_start();
	 * 
	 * @param		int		$maxLifetime		session max lifetime.
	 */
	public static function gc( $maxLifetime );
	
}
?>