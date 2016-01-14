<?php
namespace herosphp\utils;
/*---------------------------------------------------------------------
 * Ajax请求结果数据返回
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

class AjaxResult {

    /**
     * 状态
     * @var string
     * ok => 成功, error => 失败
     */
    private $state;

    /**
     * 消息
     * @var string
     */
    private $message;

    /**
     * 数据
     * @var array
     */
    private $data;

    /**
     * 显示ajax操作失败默认结果
     */
    public static function ajaxFailtureResult(){
        $result = new AjaxResult('error', "操作失败!");
        die($result->toJsonMessage());
    }

    /**
     * 显示ajax操作成功默认结果
     */
    public static function ajaxSuccessResult(){
        $result = new AjaxResult('ok', "操作成功!");
        die($result->toJsonMessage());
    }

    /**
     * 显示ajax操作结果
     */
    public static function ajaxResult($state, $message, $data=array()){
        $result = new AjaxResult($state, $message, $data);
        die($result->toJsonMessage());
    }

    /**
     * 返回jsonp数据格式
     * @param $state
     * @param $message
     * @param $callback
     */
    public static function jsonp($state, $message, $callback){
        $result = new AjaxResult($state, $message);
        die($callback. "(". $result->toJsonMessage() .")");
    }

    public function __construct($state, $message, $data){
        $this->setState($state);
        $this->setMessage($message);
        $this->setData($data);
    }


    /**
     * @return the $state
     */
    public function getState() {
        return $this->state;
    }

    /**
     * @param number $state
     */
    public function setState($state) {
        $this->state = $state;
    }

    /**
     * @return the $message
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * @return the $data
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param multitype: $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * 返回Json格式结果
     */
    public function toJsonMessage(){
        return json_encode(array('state'=>$this->getState(), 'message'=>$this->getMessage(), 'data'=>$this->getData()));
    }
}

?>
