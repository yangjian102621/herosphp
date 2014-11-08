<?php
namespace modphp\bean;

use modphp\common\ModException;
use \ReflectionClass;
use modphp\bean\exception\MethodInvokeException;
use modphp\debug\Debug;
/**
 * This class is a tool class use to service for beans
 * @author blueyb.java@gmail.com
 * @since 1.0 - 2010-12-17
 */
class BeanUtil{
	
	/**
	 * Create a object instance.
	 * @param string $class
	 */
	public static function builtInstance($clazz, $params=null){
		if(!is_string($clazz)){
			return $clazz;
		}
		try{
			$clazz = new ReflectionClass($clazz);
			if(is_array($params)){
				return $clazz->newInstanceArgs($params);
			}elseif($params){
				return $clazz->newInstance($params);
			}else{
				return $clazz->newInstance();
			}
		}catch(\Exception $e){
			throw $e;
		}
	}
	
	/**
	 * Set up the property for the bean.
	 * @param $clazz 
	 * 	String or Object type, if it is String, We will use this string to create a object.
	 * @param array $properties 
	 * 	This is a property array, it contains the propery names and values which
	 *  you want to set to the object.
	 * @return Any object which had been set up.
	 */
	public static function install($clazz, $properties){
		if(!$properties) return;
		if(is_string($clazz)){
			$refClass =new ReflectionClass($clazz);
			$obj = $refClass->newInstance();
		}else{
			// is object
			$obj = $clazz;
			$refClass =new ReflectionClass($obj);
		}
		$methodName = NULL;
		$method = NULL;
		foreach($properties as $key => $value){
			$methodName = "set" . ucwords($key);
			if($refClass->hasMethod($methodName)){
				$method = $refClass->getMethod($methodName);
				try{
					$method->invoke($obj, $properties[$key]);
				}catch(\Exception $e){
					throw new MethodInvokeException($e->getMessage());
				}
			}
		}
		return $obj;
	}
	
	/**
	 * Copy the attributs from a bean and set them to anthor bean.
	 * @access public
	 * @param stdclass $beanTo	The bean which want to setting attribute.
	 * @param stdclass $beanFrom The bean which use to be copy attributes.
	 */
	public static function attributeCopys($beanTo, $beanFrom){
		throw new ModException('attributeCopys is not support yet!');
	}
	
	/**
	 * 检查类是否有指定方法
	 * @param mixed $clazz
	 * @param string $methodName
	 * @return 如果有方法，返回true
	 */
	public static function hasMethod($clazz, $methodName){
		if(! $clazz instanceof ReflectionClass){
			$clazz =new ReflectionClass($clazz);
		}
		return $clazz->hasMethod($methodName);
	}
	
	/**
	 * 调用对象的方法，返回方法返回的值
	 * @param object $object 被调用的对象
	 * @param string $methodName 方法名
	 * @param mixed $params 参数
	 * @return mixed 返回被调用方法的返回值
	 */
	public static function invokeMethod($object, $methodName, $params){
		$clazz =new ReflectionClass($object);
		$method = $clazz->getMethod($methodName);
		if(!$method) return null;
		try{
			if(is_array($params)){
				return $method->invokeArgs($object, $params);
			}else{
				return $method->invoke($object, $params);
			}
		}catch(\Exception $e){
			throw new MethodInvokeException($e->getMessage());
		}
	}
}