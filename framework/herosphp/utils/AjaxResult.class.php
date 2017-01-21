<?php
namespace herosphp\utils;
/*---------------------------------------------------------------------
 * Ajax请求结果数据返回
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

use herosphp\string\StringUtils;

class AjaxResult {

    /**
     * 单个结果KEY
     */
    const DATA_KEY_ITEM = "item";

    /**
     * 列表结果KEY
     */
    const DATA_KEY_ITEMS = "items";

    /**
     * 数据总数KEY
     */
    const DATA_KEY_COUNT = "count";

    /**
     * 错误代码
     * @var int
     * 000 => 成功, 001 => 失败
     */
    private $code;

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



    //code for operation successfully
    const OP_SUCCESS = '000';

    //default code for operation failed
    const OP_FAILURE = '001';

    //error code 101: Invalid parameter operation
    const INVALID_PARAM = '101';

    /**
     * 显示ajax操作失败默认结果
     */
    public static function ajaxFailtureResult(){
        $result = new AjaxResult(self::OP_FAILURE, "操作失败.");
        die($result->toJsonMessage());
    }

    /**
     * 显示ajax操作成功默认结果
     */
    public static function ajaxSuccessResult(){
        $result = new AjaxResult(self::OP_SUCCESS, "操作成功.");
        die($result->toJsonMessage());
    }

    /**
     * 显示ajax操作结果
     */
    public static function ajaxResult($code, $message, $data=array()){
        $result = new AjaxResult($code, $message, $data);
        die($result->toJsonMessage());
    }

    /**
     * 返回一个成功的 result vo
     * @param $message
     * @param $data
     * @return AjaxResult
     */
    public static function success($message='处理成功', $data=array()) {
        return new AjaxResult(self::OP_SUCCESS, $message, $data);
    }

    /**
     * 返回一个失败的 result vo
     * @param $message
     * @param $data
     * @return AjaxResult
     */
    public static function fail($message,$data) {
        return new AjaxResult(self::OP_FAILURE, $message, $data);
    }

    /**
     * 返回jsonp数据格式
     * @param $code
     * @param $message
     * @param $callback
     */
    public static function jsonp($code, $message, $callback){
        $result = new AjaxResult($code, $message);
        die($callback. "(". $result->toJsonMessage() .")");
    }

    public function __construct($code, $message, $data){
        $this->setCode($code);
        $this->setMessage($message);
        $this->setData($data);
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->code = $code;
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
     * add data to result set
     * @param key
     * @param value
     */
    public function putData($key, $value) {
        $this->data[$key] = $value;
    }


    public function putItem($item){
        $this->putData(self::DATA_KEY_ITEM, $item);
    }

    public function putItems($items){
        $this->putData(self::DATA_KEY_ITEMS, $items);
    }

    public function putCount($value){
        $this->putData(self::DATA_KEY_COUNT, $value);
    }

    public function getItems() {
        return $this->data[self::DATA_KEY_ITEMS];
    }

    public function getItem() {
        return $this->data[self::DATA_KEY_ITEM];
    }

    public function getCount() {
        return $this->data[self::DATA_KEY_COUNT];
    }

    /**
     * 判断是否成功
     * @return bool
     */
    public function isSucess() {
        return $this->code == self::OP_SUCCESS;
    }

    /**
     * 返回Json格式结果
     */
    public function toJsonMessage(){
        return StringUtils::jsonEncode(array('code'=>$this->getCode(), 'message'=>$this->getMessage(), 'data'=>$this->getData()));
    }
}