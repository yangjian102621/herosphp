<?php
/*---------------------------------------------------------------------
 * session handler for file. save session data to file by user
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
class FileSession implements ISession {

    /**
     * @var array $config session配置参数
     */
    private static $config;

    /**
     * @var string $sessionSavePath session 文件保存路径
     */
    private static $sessionSavePath;

    /**
     * @var int 当前时间
     */
    private static $ctime;

    /**
     * @var string 用户客户端ip
     */
    private static $userIp;
	
	/**
	 * @see	\herosphp\session\interfaces\ISession::start().
	 */
	public static function start( $config = NULL ) {

        //初始化配置信息
        if ( !$config ) {
            self::$config = array(
                'session_file_prefix'	=> 'heros_sess_',
                'session_update_interval' => 30,
                'gc_maxlifetime' => 1440
            );
        } else {
            self::$config = $config;
        }
        if ( !self::$config['gc_maxlifetime'] ) {
            self::$config['gc_maxlifetime'] = ini_get('session.gc_maxlifetime');
        }
        //初始化用户ip
        self::$userIp = $_SERVER['REMOTE_ADDR'];

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

		if ( !self::$sessionSavePath ) {

			self::$sessionSavePath = $savePath;
			//创建session目录
			if ( !file_exists(self::$sessionSavePath) )
				@mkdir(self::$sessionSavePath);
			}
		//do nothing here
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
		
		$sessionFile = self::$sessionSavePath.DIRECTORY_SEPARATOR.self::$config['session_file_prefix'].$sessionId;

		if ( file_exists($sessionFile) ) {
            //1.if the session data is invalid, destroy session.
			if ( filemtime($sessionFile) + self::$config['gc_maxlifetime'] < self::$ctime ) {
				self::destroy($sessionId);
				return '';
			}
			return file_get_contents($sessionFile);
		}
		
		//2. if user's ip address is changed, destroy session.
		if ( $_SERVER['REMOTE_ADDR'] != self::$userIp ) {
			self::destroy($sessionId);
			return '';
		}
		return '';
	}
	
	/**
	 * @see	\herosphp\session\interfaces\ISession::write().
	 */
	public static function write( $sessionId, $data ) {
		
		$sessionFile = self::$sessionSavePath.DIRECTORY_SEPARATOR.self::$config['session_file_prefix'].$sessionId;
        //先获取session数据
        $sessionData = file_get_contents($sessionFile);
        //为减少服务器的负担，每30秒钟更新一次session或者session有改变时
        if ( $sessionData != $data
            || (filemtime($sessionFile) + self::$config['session_update_interval']) < self::$ctime ) {

            return file_put_contents($sessionFile, $data);
        }

        return true;

	}
	
	/**
	 * @see	\herosphp\session\interfaces\ISession::destroy().
	 */
	public static function destroy( $sessionId ) {

        //删除session文件
		$sessionFile = self::$sessionSavePath.DIRECTORY_SEPARATOR.self::$config['session_file_prefix'].$sessionId;
		if ( file_exists($sessionFile) ) {
			return @unlink($sessionFile);
		}
        return false;
	}
	
	/**
	 * @see	\herosphp\session\interfaces\ISession::gc().
	 */
	public static function gc( $maxLifeTime ) {

		$sessionFiles = glob( self::$sessionSavePath.DIRECTORY_SEPARATOR.self::$config['session_file_prefix'].'*' );
		if ( !empty($sessionFiles) ) {
			foreach ( $sessionFiles as $value ) {
				if ( filemtime($value) + $maxLifeTime < time() ) {
					@unlink($value);
				}
			}
		}
		return true;
	}
	
}
?>