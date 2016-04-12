<?php
/*---------------------------------------------------------------------
 * session handler for file. save session data to file by user
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @since 2015.02.20
 *-----------------------------------------------------------------------*/
namespace herosphp\session;

use herosphp\core\Loader;
use herosphp\session\interfaces\ISession;
use herosphp\utils\FileUtils;

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
     * @var string 用户客户端ip
     */
    private static $userIp;

    /**
     * @see    \herosphp\session\interfaces\ISession::start().
     * @param null $config
     * @return mixed|void
     */
	public static function start( $config = null ) {

        //初始化配置信息
		self::$config = $config;
		self::$sessionSavePath = $config["session_save_path"];
		if ( !file_exists(self::$sessionSavePath) ) {
			FileUtils::makeFileDirs(self::$sessionSavePath);
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
		@session_start();
	}

    /**
     * @see    \herosphp\session\interfaces\ISession::open().
     * @param string $savePath
     * @param string $sessionName
     * @return bool
     */
	public static function open( $savePath, $sessionName ) {

		//创建session目录
		if (!file_exists(self::$sessionSavePath)) {
			FileUtils::makeFileDirs(self::$sessionSavePath);
			if ( !is_writable(self::$sessionSavePath) ) {
				E("session 目录".self::$sessionSavePath."不可写，请更改权限。");
			}
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
     * @see    \herosphp\session\interfaces\ISession::read().
     * @param string $sessionId
     * @return string
     */
	public static function read( $sessionId ) {

		$sessionFile = self::$sessionSavePath.DIRECTORY_SEPARATOR.self::$config['session_file_prefix'].$sessionId;

		if ( file_exists($sessionFile) ) {
            //1.if the session data is invalid, destroy session.
			if ( filemtime($sessionFile) + self::$config['gc_maxlifetime'] < time() ) {
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
     * @see    \herosphp\session\interfaces\ISession::write().
     * @param string $sessionId
     * @param array|string $data
     * @return bool|int
     */
	public static function write( $sessionId, $data ) {

		$sessionFile = self::$sessionSavePath.DIRECTORY_SEPARATOR.self::$config['session_file_prefix'].$sessionId;
        //先获取session数据
        $sessionData = file_get_contents($sessionFile);
        //为减少服务器的负担，每30秒钟更新一次session或者session有改变时
        if ( $sessionData != $data
            || (filemtime($sessionFile) + self::$config['session_update_interval']) < time() ) {

            return file_put_contents($sessionFile, $data);
        }

        return true;

	}

    /**
     * @see    \herosphp\session\interfaces\ISession::destroy().
     * @param string $sessionId
     * @return bool
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
     * @see    \herosphp\session\interfaces\ISession::gc().
     * @param int $maxLifeTime
     * @return bool
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