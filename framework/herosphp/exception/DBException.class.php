<?php
namespace herosphp\exception;
/**
 * 数据库异常处理类
 */
class DBException extends HeroException {

    protected $query;       /* 查询语句 */

    public function __contruct( $message ) {
        parent::__contruct($message);
    }

    /**
     * @param mixed $query
     */
    public function setQuery($query)
    {
        $this->query = $query;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return $this->query;
    }

}