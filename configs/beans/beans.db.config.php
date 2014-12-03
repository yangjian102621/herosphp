<?php

use modphp\bean\Beans;

/**
 * 数据库bean装配配置
 * @author blueyb.java@gmail.com
 * @since 1.0 - Nov 26, 2012
 */
$beans = array(
	'db' => array(
		'@type'=>Beans::TYPE_OBJECT,
		'@class' => 'modphp\db\SingleDB',
		'@attributes' => array(
			'config'=>array(
				'db_type' => 'mysql',
				'db_host' => 'localhost',
				'db_name' => 'juke123',
				'db_user' => 'root',
				'db_pass' => '123456',
				'db_charset' => 'UTF8'	
			)
		),
		'@invokes' => array(
			'connect'
		)
	)
);
return $beans;
