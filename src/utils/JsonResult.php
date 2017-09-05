<?php
/**
 * json result vo
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since v1.2.1
 */
namespace herosphp\utils;

use herosphp\string\StringUtils;

class JsonResult {

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
     */
    private $_code = 200;

    /**
     * 状态码信息
     * @var array
     */
    private static $_CODE_STATUS = [
        200 => 'OK.',
        201 => 'Created.',
        204 => 'No Contents.',
        400 => 'Bad Request.',
        401 => 'Not Authorized.',
        503 => 'Access Forbidden.',
        404 => 'Not Found.',
        405 => 'Method Not Allow.',
        500 => 'Server internal Error.',
    ];

    /**
     * 消息
     * @var string
     */
    private $_message;

    /**

    /**
     * 数据
     * @var array
     */
    private $_data;

    /**
     * JsonResult constructor.
     * @param $code
     * @param $message
     * @param $data
     */
    public function __construct($code, $message, $data){
        $this->setCode($code);
        $this->setMessage($message);
        $this->setData($data);
    }

    /**
     * 创建 JsonResult 实例, 并输出
     * @param $code
     * @param $message
     * @param array $data
     * @return JsonResult
     */
    public static function result($code, $message, $data=array()) {
        $result = new self($code, $message, $data);
        $result->output();
    }

    /**
     * 返回一个成功的 result vo
     * @param string $message
     * @param array $data
     * @return JsonResult
     */
    public static function success($message='操作成功', $data=array()) {
        $result = new self(200, $message, $data);
        $result->output();
    }

    /**
     * 返回一个失败的 result vo
     * @param string $message
     * @param $data
     * @return JsonResult
     */
    public static function fail($message='系统开了小差', $data) {
        $result = new self(500, $message, $data);
        $result->output();
    }

    /**
     * 返回jsonp数据格式
     * @param $code
     * @param $message
     * @param $callback
     */
    public static function jsonp($code, $message, $callback){
        $result = new self($code, $message);
        die($callback. "(". $result .")");
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @param int $code
     */
    public function setCode($code)
    {
        $this->_code = $code;
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
     * @return the $data
     */
    public function getData() {
        return $this->_data;
    }

    /**
     * @param multitype: $data
     */
    public function setData($data) {
        $this->_data = $data;
    }

    /**
     * add data to result set
     * @param key
     * @param value
     */
    public function putData($key, $value) {
        $this->_data[$key] = $value;
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
        return $this->_data[self::DATA_KEY_ITEMS];
    }

    public function getItem() {
        return $this->_data[self::DATA_KEY_ITEM];
    }

    public function getCount() {
        return $this->_data[self::DATA_KEY_COUNT];
    }

    /**
     * 判断是否成功
     * @return bool
     */
    public function isSucess() {
        return $this->_code == 200;
    }

    /**
     * 转换字符串
     * @return string
     */
    public function __toString() {
        if ( !$this->getMessage() ) {
            $this->setMessage(self::$_CODE_STATUS[$this->_code]);
        }
        return StringUtils::jsonEncode(array('code'=>$this->getCode(), 'message'=>$this->getMessage(), 'data'=>$this->getData()));
    }

    /**
     * 以json格式输出
     */
    public function output() {
        header('Content-type: application/json;charset=utf-8');
        echo $this;
        die();
    }
}