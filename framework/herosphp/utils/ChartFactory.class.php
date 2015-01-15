<?php
namespace herosphp\utils;
/**
 * 图表生成工厂类
 * The factory class to make chart
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@163.com>
 * @completed 	2013.04.14
 * @version 	1.0
 */
class ChartFactory {
	
	/* instance array of chart class */
	private static $_instance = array();
	
	/* create chart instance */
	public static function create( $_key, &$_config ) {
		$_className = ucfirst($_key).'Chart';
		$_DIR = dirname(__FILE__).DIR_OS;
		$_classFile = $_DIR.'chart'.DIR_OS.$_className.'.class.php';
		include $_DIR.'chart'.DIR_OS.'IChart.class.php';
		include $_classFile;
		//如果该实例已经创建, 则直接返回实例.
		if ( !isset(self::$_instance[$_key]) ) {
			self::$_instance[$_key] = new $_className($_config);
		}
		return self::$_instance[$_key];
	}
	
}
?>