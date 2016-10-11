<?php
/**
 * session configures for all session handlers.
 * @author yangjian<yangjian102621@gmail.com>
 */

return array (

	/**
	 * 定义session介质
	 * 1. file => 文件介质存储 (default)
	 * 2. memo => memcache介质存储
	 * 3. redis => redis介质存储
	 */
	'session_handler' => 'file',

	//file session configure
	'file' => array(
		'session_file_prefix' => 'heros_session_',		/* session file prefix */
		'session_update_interval' => 30,				/* session update interval */
		'session_save_path' => APP_RUNTIME_PATH."session",				/* session文件保存路径 */
		'gc_maxlifetime' => 3600,				/* session gc lifetime */
	),
	
	//memcache session configure
	'memo' => array(
		'host'	=> '127.0.0.1',
		'port'  => '11211',
        'gc_maxlifetime' => 3600,				/* session gc lifetime */
	),

	//redis session configure
	'redis' => array(
		'host' => '127.0.0.1',
		'port' => 6379,
		'gc_maxlifetime' => 3600,				/* session gc lifetime */
	)
);