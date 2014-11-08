<?php

/**
 * Ajax请求结果
 * @author			yangjian<yangjian102621@gmail.com>
 * @since			2013-12-28
 
 * 
 */
class AjaxResult {
	
	/**
	 * 状态
	 * @var string
	 * ok => 成功, error => 失败
	 */
	private $_state;
	
	/**
	 * 消息
	 * @var string
	 */
	private $_message;
	
	/**
	 * 显示ajax操作失败默认结果
	 */
	public static function ajaxError(){
		$result = new self('error', "操作失败!");
		die($result->toJsonMessage());
	}
	
	/**
	 * 显示ajax操作成功默认结果
	 */
	public static function ajaxOk(){
		$result = new self('ok', "操作成功!");
		die($result->toJsonMessage());
	}
	
	/**
	 * 显示ajax操作结果
	 */
	public static function ajaxResult($state, $message){
		$result = new self($state, $message);
		die($result->toJsonMessage());
	}
	
    //构造方法
	public function __construct($state, $message){
		$this->setState($state);
		$this->setMessage($message);
	}
	
	/**
	 * @return the $state
	 */
	public function getState() {
		return $this->_state;
	}

	/**
	 * @param number $state
	 */
	public function setState($state) {
		$this->_state = $state;
	}

	/**
	 * @return the $message
	 */
	public function getMessage() {
		return $this->_message;
	}

	/**
	 * @param string $message
	 */
	public function setMessage($message) {
		$this->_message = $message;
	}
	
	/**
	 * 返回Json格式结果
	 */
	public function toJsonMessage(){
		return json_encode(array('state'=>$this->getState(), 'message'=>$this->getMessage()));
	}
}

?>
