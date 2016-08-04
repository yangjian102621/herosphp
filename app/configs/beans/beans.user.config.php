<?php
use herosphp\bean\Beans;
/**
 * user模块Beans装配配置
 * @author yangjian<yangjian102621@gmail.com>
 */
$beans = array(
	//AdminService configs
	'user.user.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'user\service\UserService',
		'@attributes' => array(
			'@bean/modelDao'=>array(
				'@type'=>Beans::BEAN_OBJECT,
				'@class'=>'user\dao\UserDao',
				'@params' => array('User','News','Admin')
			)
		),
	),
	//AdminService configs
	'user.news.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'user\service\NewsService',
		'@attributes' => array(
			'@bean/modelDao'=>array(
				'@type'=>Beans::BEAN_OBJECT,
				'@class'=>'user\dao\NewsDao',
				'@params' => array('News')
			)
		),
	),
	//AdminService configs
	'user.admin.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'user\service\AdminService',
		'@attributes' => array(
			'@bean/modelDao'=>array(
				'@type'=>Beans::BEAN_OBJECT,
				'@class'=>'user\dao\AdminDao',
				'@params' => array('Admin')
			)
		),
	),
);
return $beans;
