<?php
namespace modphp\bean;

use modphp\bean\exception\BeanConfigException;

/**
 * 这个类主要负责创建和管理Bean对象,在这里Bean包括PHP里的所有数据类型，可以是对象，也可以是基本类型或数组。
 * @author blueyb.java@gmail.com
 * @since 1.0 - 2012-11-25
 */
abstract class Beans{
	
	/**
	 * Bean的类型:对象
	 * @var string
	 */
	const TYPE_OBJECT = 'Object';
	
	/**
	 * Bean的类型:对象数组
	 * @var string
	 */
	const TYPE_OBJECT_ARRAY = 'ObjectArray';
	
	/**
	 * 应用程序监听器的Bean名称
	 * @var string
	 */
	const MODPHP_APPLICATION_LISTENERS = 'modphp_application_listeners';
	
	/**
	 * 分发控制器的Bean名称
	 * @var string
	 */
	const MODPHP_CONTROLLER = 'modphp_controller';
	
	/**
	 * Bean装配配置
	 * @var array
	 */
	private static $configs = array();
	
	/**
	 * Bean容器
	 * @var array
	 */
	private static $beans = array();
	
	/**
	 * 获取指定ID的Bean
	 * @param string $id Bean的ID
	 * @param boolean $new 是否创建新的bean,新创建的Bean不会放到容器中，如果要放到容器中，请使用set方法。
	 * @return mixed 返回指定ID的Bean,如果Bean不存在则返回null
	 */
	public static function get($id, $new=false){
		$beanConfig = self::$configs[$id];
		if(!$beanConfig) return null;
		if($new || $beanConfig['@single'] === false){
			return self::build($beanConfig);
		}
		if(!isset(self::$beans[$id])){
			self::$beans[$id] = self::build($beanConfig);
		}
		return self::$beans[$id];
	}
	
	/**
	 * 把Bean放到容器中进行管理，如果已存在指定ID的Bean则会返回原来的Bean
	 * @param string $id Bean的ID
	 * @param mixed $bean 要放到Bean容器中的Bean
	 * @return mixed 如果已存在指定ID的Bean则会返回原来的Bean，否则不返回任何值。
	 */
	public static function set($id, $bean){
		if(self::$beans[$id]){
			$oldBean = self::$beans[$id];
			self::$beans[$id] = $bean;
			return $oldBean;
		}else{
			self::$beans[$id] = $bean;
		}
	}
	
	/**
	 * 删除指定ID的Bean并返回被删除的Bean
	 * @param string $id
	 * @return mixed 返回被删除的Bean
	 */
	public static function delete($id){
		$bean = self::$beans[$id];
		return $bean;
	}
	
	/**
	 * 设置Bean装配配置
	 * @param array: $configs
	 */
	public static function setConfigs(&$configs){
		self::$configs = $configs;
	}
	
	/**
	 * 根据Bean装配配置来创建Bean
	 * @param array $beanConfig Bean装配配置
	 * @return mixed 返回创建的Bean
	 */
	private static function build($beanConfig){
		if(is_array($beanConfig) && $beanConfig['@type']){
			switch ($beanConfig['@type']){
				case Beans::TYPE_OBJECT:
					//是对象
					$bean = BeanUtil::builtInstance($beanConfig['@class'], $beanConfig['@params']);
					//属性装载
					$attributes = $beanConfig['@attributes'];
					if($attributes){
						self::attributesInstall($bean, $attributes);
					}
					//方法调用
					$invokes = $beanConfig['@invokes'];
					if($invokes){
						self::methodsInvoke($bean, $invokes);
					}
					return $bean;
					break;
				case Beans::TYPE_OBJECT_ARRAY:
					$beans = array();
					$bean = null;
					foreach($beanConfig['@attributes'] as $beanKey=>$subBeanConfig){
						$bean = self::build($subBeanConfig);
						$beans[$beanKey] = $bean;
					}
					return $beans;
					break;
			}
		}else{
			//普通数据
			return $beanConfig;
		}
	}

    /**
     * 根据属性配置装配Bean属性
     * @param Object $bean Bean对象
     * @param array $attributes 属性配置
     * @throws exception\BeanConfigException
     */
	private static function attributesInstall($bean, $attributes){
		$attributesToInstall = array();	//用来存放处理过后的属性
		foreach($attributes as $attributeName=>$attributeValue){
			if($attributeName[0] == '@'){
				//以@开头的属性名为需要进一步处理的属性
				$attributeNames = explode('/', $attributeName);
				if(count($attributeNames) != 2) continue;
				$attributeName = $attributeNames[1];
				switch($attributeNames[0]){
					case '@id':
						//属性值是一个id指定的Bean
						$attributeValue = self::get($attributeValue);
						break;
					case '@bean':
						//属性值是一个@bean配置项目，需要创建这个Bean
						if(!is_array($attributeValue)){
							throw new BeanConfigException("Bean属性配置错误!", $bean, $attributeValue);
						}
						$attributeValue = self::build($attributeValue);
						break;
					default:
						continue;
						break;
				}
			}
			$attributesToInstall[$attributeName] = $attributeValue;
		}
		BeanUtil::install($bean, $attributesToInstall);
	}

    /**
     * 根据方法调用配置调用指定Bean的方法
     * @param Object $bean Bean对象
     * @param array $invokes 方法调用配置
     * @throws exception\BeanConfigException
     */
	private static function methodsInvoke($bean, $invokes){
		foreach($invokes as $method=>$params){
			if(is_int($method)){
				$method = $params;
				$params = null;
			}elseif($method[0] == '@'){
				//以@开头的方法名为需要进一步处理的方法
				$methods = explode('/', $method);
				if(count($methods) != 2) continue;
				$method = $methods[1];
				switch($methods[0]){
					case '@id':
						//参数是一个id指定的Bean
						$params = Beans::get($params);
						break;
					case '@bean':
						//参数是一个@bean配置项目，需要创建这个Bean
						if(!is_array($params)){
							throw new BeanConfigException("Bean配置错误!", $bean, $params);
						}
						$params = Beans::build($params);
						break;
					default:
						continue;
						break;
				}
			}
			BeanUtil::invokeMethod($bean, $method, $params);
		}
	}
}
?>