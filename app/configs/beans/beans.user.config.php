<?php
use herosphp\bean\Beans;
/**
 * user模块Beans装配配置
 * @author yangjian<yangjian102621@gmail.com>
 */
$beans = array(
	//user服务
	'user.user.service' => array(
		'@type' => Beans::BEAN_OBJECT,
		'@class' => 'user\service\UserService',
		'@attributes' => array(
			'@bean/modelDao'=>array(
				'@type'=>Beans::BEAN_OBJECT,
				'@class'=>'user\dao\UserDao',
				'@params' => array('News','Admin','AdminRole','User')
			)
		),
	),
	//news服务
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
	//admin服务
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
