<?php
use herosphp\bean\Beans;
/**
 * user模块Beans装配配置
 * @author yangjian<yangjian102621@gmail.com>
 */
$beans = array(
	'test.user.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'test\service\UserService',
		'@params' => array('User')
	)
);
return $beans;
