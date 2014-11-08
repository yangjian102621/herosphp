<?php
namespace modphp\bean\exception;

use modphp\common\ModException;
/**
 * Bean属性配置错误
 * @author blueyb.java@gmail.com
 * @since 1.0 - 2013-06-14
 */

class BeanConfigException extends ModException{
	
	/**
	 * Bean
	 * @var
	 */
	private $bean;
	
	/**
	 * 属性
	 * @var mixed
	 */
	private $attributes;
	
	function __construct($message, $bean, $attributes){
		parent::__construct($message);
		$this->setBean($bean);
		$this->setAttributes($attributes);
	}
	
	/**
	 * @return the $bean
	 */
	public function getBean() {
		return $this->bean;
	}

	/**
	 * @return the $attributes
	 */
	public function getAttributes() {
		return $this->attributes;
	}

	/**
	 * @param field_type $bean
	 */
	public function setBean($bean) {
		$this->bean = $bean;
	}

	/**
	 * @param \modphp\bean\exception\mixed $attributes
	 */
	public function setAttributes($attributes) {
		$this->attributes = $attributes;
	}

	
	
}