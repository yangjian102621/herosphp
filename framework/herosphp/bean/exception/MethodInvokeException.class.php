<?php
namespace modphp\bean\exception;

use modphp\common\ModException;
/**
 * 方法调用异常
 * @author blueyb.java@gmail.com
 * @since 1.0 - 2013-06-14
 */

class MethodInvokeException extends ModException{
	
	function __construct($message){
		parent::__construct($message);
	}
	
	/**
	 * 设置产生异常的方法
	 * @param string $method
	 */
	public function setMethod($method=''){
		$this->putData('method', $method);
	}
	
	/**
	 * 获取产生异常的方法
	 * @return multitype:
	 */
	public function getMethod(){
		return $this->getData('method');
	}
}