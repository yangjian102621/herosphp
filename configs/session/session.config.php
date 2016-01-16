<?php
/**
 * session configures for all session handlers.
 * @author yangjian<yangjian102621@gmail.com>
 */

return array(
	//file session configure
	'file' => array(
		'session_file_prefix' => 'heros_session_',		/* session file prefix */
		'session_update_interval' => 30,				/* session update interval */
		'gc_maxlifetime' => 1440,				/* session gc lifetime */
	),
	
	//memcache session configure
	'memo' => array(
		'host'	=> '127.0.0.1',
		'port'  => '11211',
        'gc_maxlifetime' => 1440,				/* session gc lifetime */
	)
);