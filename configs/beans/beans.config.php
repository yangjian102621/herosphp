<?php

use modphp\bean\Beans;

/**
 * Beans装配配置
 * @author blueyb.java@gmail.com
 * @since 1.0 - Nov 26, 2012
 */
$beans = array(
	Beans::MODPHP_APPLICATION_LISTENERS => array(
			'@type'=>Beans::TYPE_OBJECT_ARRAY,
			'@attributes' => array(
                array(
                    '@type'=>Beans::TYPE_OBJECT,
                    '@class'=>'common\listener\URLParseListener'
                ),
				array(
					'@type'=>Beans::TYPE_OBJECT,
					'@class'=>'common\listener\WebApplicationListener'
				)
			)
	),
	Beans::MODPHP_CONTROLLER => array(
		'@type'=>Beans::TYPE_OBJECT,
		'@class' => 'modphp\controller\Controller',
		'@attributes' => array(
			'@id/actionMatcher'=>'modphp.action.matcher'
		)
	),
	'modphp.action.matcher'=>array(
			'@type'=>Beans::TYPE_OBJECT,
			'@class' => 'modphp\controller\matcher\ContextMatcher',
			'@attributes' => array(
				'uriToActionMapping'=>array(
					'/'=>array('module'=>'common', 'action'=>'index', 'method'=>'index')
				)
			)
	)
);
return $beans;
