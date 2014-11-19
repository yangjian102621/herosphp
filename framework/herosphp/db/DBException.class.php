<?php
namespace modphp\db;

use modphp\common\ModException;
/**
 * 数据库异常处理类
 */
class DBException extends ModException {

    protected $query;       /* 查询语句 */

    public function __contruct( $message ) {
        parent::__contruct($message);
    }

    /**
     * 设置SQL
     */
    public  function  setQuery( $query ) {
        $this->query = $query;
    }

    /**
     * 设置错误代码
     */
    public  function  setCode( $code ) {
        $this->code = $code;
    }

}