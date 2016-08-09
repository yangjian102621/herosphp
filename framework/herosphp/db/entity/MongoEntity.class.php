<?php

namespace herosphp\db\entity;

/*---------------------------------------------------------------------
 * 数据库查询对象的MongoDB实现
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/


use herosphp\exception\UnSupportedOperationException;

class MongoEntity implements DBEntity {

    private $table;  //数据表名称

    private $where = array(); //查询条件

    //当前页
    private $page = 0;

    //每页记录条数
    private $pagesize = 20;

    //排序方式
    private $order;

    //查询字段
    private $fields = "*";

    private $data = array();

    private function __construct(){}

    //创建实例
    public static function getInstance() {
        return new  self();
    }

    /**
     * create query conditions and query field, order, limit...
     * @throws UnSupportedOperationException
     */
    public function buildQueryString()
    {
        throw new UnSupportedOperationException();
    }

    /**
     * build query condition
     * @return
     */
    public function buildWhere()
    {
        return $this->where;
    }

    /**
     * add AND query conditions
     * @param $field
     * @param $value
     * @return
     */
    public function addWhere($field, $value)
    {
        $this->where[$field] = $value;
        return $this;
    }

    /**
     * add OR query condition
     * @param $field
     * @param $value
     * @throws UnSupportedOperationException
     */
    public function addOrWhere($field, $value)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * add LIKE keyword query condition
     * @param $field
     * @param $value
     * @throws UnSupportedOperationException
     */
    public function addLikeWhere($field, $value)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * add OR LIKE query condition
     * @param $field
     * @param $value
     * @throws UnSupportedOperationException
     */
    public function addOrLikeWhere($field, $value)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * add special operation condition, such as >= <= != and so on
     * @param $field
     * @param $opt
     * @param $value
     * @throws UnSupportedOperationException
     */
    public function addOptWhere($field, $opt, $value)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * add OR special operation condition, such as >= <= != and so on
     * @param $field
     * @param $opt
     * @param $value
     * @throws UnSupportedOperationException
     */
    public function addOrOptWhere($field, $opt, $value)
    {
        throw new UnSupportedOperationException();
    }

    public function pagesize($pagesize)
    {
        if ( $pagesize > 0 ) $this->pagesize = $pagesize;
        return $this;
    }

    public function field($field)
    {
        if ( $field ) $this->fields = $field;
        return $this;
    }

    public function page($page)
    {
        if ( $page > 0 ) $this->page = $page;
        return $this;
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function group($group)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * <p>直接设置查询条件字符串, 如果设置了这个条件，其他条件都将失效</p>
     * @param $where
     * @throws UnSupportedOperationException
     */
    public function where($where)
    {
        throw new UnSupportedOperationException();
    }

    public function having($having)
    {
        throw new UnSupportedOperationException();
    }

    public function setTable($table)
    {
        $this->table = $table;
        return $this;
    }

    public function getTable()
    {
        return $this->table;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function getData()
    {
        return $this->data;
    }
}
