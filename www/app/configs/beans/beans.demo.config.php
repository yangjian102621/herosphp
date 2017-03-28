<?php
use herosphp\bean\Beans;
/**
 * user模块Beans装配配置
 * @author yangjian<yangjian102621@gmail.com>
 */
$beans = array(
	'demo.user.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'demo\service\UserService',
		'@params' => array('User')
	),
	'api.user.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'api\service\UserService',
	),
	'api.shop.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'api\service\ShopService',
	),
	'demo.shop.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'demo\service\shop',
		'@params' => array('User')
	),
	//{beansTag}

);
return $beans;
