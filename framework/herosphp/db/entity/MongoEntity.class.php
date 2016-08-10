<?php

namespace herosphp\db\entity;

/*---------------------------------------------------------------------
 *  数据库操作实体类(Entity)的MongoDB实现
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
    private $page = 1;

    //每页记录条数
    private $pagesize = 20;

    //排序方式
    private $order = array();

    //查询字段
    private $fields = array();

    //要插入或者更新的数据
    private $data = array();

    ////插入或更新操作选项
    private $options = array(
        'fsync' => 0, //是否强制同步写入,mongodb为了保证性能，写入是异步的，先时保存在内存的
        'upsert' => 0, //更新的时候没有符合条件的文档是否创建一条新文档
        'multiple' => 1, //是否更新所有匹配的文档，默认只更新匹配的第一条
        'justOne' => 0, //是否只删除一个
    );

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
     * @return $this
     */
    public function addOrWhere($field, $value)
    {
        $this->where['$or'][] = array($field => $value);
        return $this;
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

    public function getPagesize() {return $this->pagesize;}

    public function field($field)
    {
        if ( is_array($field) ) {
            $this->fields = $field;
        } else {
            $__fields = explode(',', $field);
            foreach ( $__fields as $value ) {
                $this->fields[trim($value)] = 1;
            }
        }
        return $this;
    }

    public function getFields() {
        return $this->fields;
    }

    public function page($page)
    {
        if ( $page > 0 ) $this->page = $page;
        return $this;
    }

    public function getPage() {return $this->page;}

    public function order($order)
    {
        if ( is_array($order) ) {
            $this->order = $order;
        } else {
            $oarr = explode(',', $order);
            foreach ($oarr as $value) {
                $value = preg_replace('/\s+/', ' ', $value);    //去除多余的空格
                $value = explode(' ', $value);
                if ( strtoupper($value[1]) == "DESC" ) {
                    $this->order[$value[0]] = -1;
                } else {
                    $this->order[$value[0]] = 1;
                }
            }
        }
        return $this;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function group($group)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * <p>直接设置查询条件字符串, 如果设置了这个条件，其他条件都将失效</p>
     * @param $where
     * @return $this
     */
    public function where($where)
    {
        $this->where = $where;
        return $this;
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

    public function addOptions($key, $value) {
        $this->options[$key] = $value;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
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
