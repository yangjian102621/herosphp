<?php

namespace herosphp\db\query;

/*---------------------------------------------------------------------
 * 数据库查询对象实现
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/


use herosphp\string\StringBuffer;

class MQuery implements IQuery {

    private $_table = null;  //数据表名称

    //关联表名称，如果关联表不为空，则会自动使用联表查询
    private $unionTable = '';

    /**
     * quyery condition map
     * @var StringBuffer|null
     */
    private $where = null;

    //query condition string, if is not null, the query condition does not used
    private $whereString = null;

    //当前页
    private $page = 0;

    //每页记录条数
    private $pagesize = 20;

    //排序方式
    private $order = null;

    //分组方式
    private $group = null;

    //分组筛选条件
    private $having = null;

    //查询字段
    private $fields = "*";

    public function __construct()
    {
        $this->where = new StringBuffer();
    }

    //创建实例
    public static function getInstance() {
        return new  self();
    }

    /**
     * create query conditions and query field, order, limit...
     * @return
     */
    public function buildQueryString()
    {
        $query = new StringBuffer("SELECT {$this->fields} FROM ");
        if ( $this->unionTable != '' ) {
            $query->append($this->unionTable);
        } else {
            $query->append($this->_table);
        }

        if ( !$this->where->isEmpty() || $this->whereString != null ) $query->append(" WHERE ".$this->buildWhere());
        if ( $this->group ) $query->append(" GROUP BY ".$this->group);
        if ( $this->having ) $query->append(" HAVING ".$this->having);
        if ( $this->order ) $query->append(" ORDER BY ".$this->order);
        //创建limit
        if ( $this->pagesize > 0 && $this->page ) {
            $offset = ($this->page-1) * $this->pagesize;
			$query->append(" LIMIT ".$offset.",".$this->pagesize);
		}
        return $query->toString();
    }

    /**
     * build query condition
     * @return
     */
    public function buildWhere()
    {
        if ( $this->whereString != null ) {
            return $this->whereString;
        } else {
            return $this->where->toString();
        }
    }

    /**
     * add AND query conditions
     * @param $field
     * @param $value
     * @return
     */
    public function addWhere($field, $value)
    {
        $this->addJoinStr(" AND ");
        $this->where->append($field."=".$this->getFieldValue($value));
        return $this;
    }

    /**
     * add OR query condition
     * @param $field
     * @param $value
     * @return
     */
    public function addOrWhere($field, $value)
    {
        $this->addJoinStr(" OR ");
        $this->where->append($field."=".$this->getFieldValue($value));
        return $this;
    }

    /**
     * add LIKE keyword query condition
     * @param $field
     * @param $value
     * @return
     */
    public function addLikeWhere($field, $value)
    {
        $this->addJoinStr(" AND ");
        $this->where->append("{$field} LIKE '%{$value}%'");
        return $this;
    }

    /**
     * add OR LIKE query condition
     * @param $field
     * @param $value
     * @return
     */
    public function addOrLikeWhere($field, $value)
    {
        $this->addJoinStr(" OR ");
        $this->where->append("{$field} LIKE '%{$value}%'");
        return $this;
    }

    /**
     * add special operation condition, such as >= <= != and so on
     * @param $field
     * @param $opt
     * @param $value
     * @return
     */
    public function addOptWhere($field, $opt, $value)
    {
        $this->addJoinStr(" AND ");
        $this->where->append("{$field} {$opt} ".$this->getFieldValue($value));
        return $this;
    }

    /**
     * add OR special operation condition, such as >= <= != and so on
     * @param $field
     * @param $opt
     * @param $value
     * @return
     */
    public function addOrOptWhere($field, $opt, $value)
    {
        $this->addJoinStr(" OR ");
        $this->where->append("{$field} {$opt} ".$this->getFieldValue($value));
        return $this;
    }

    /**
     * add left brackets(添加左边括号)
     * @param $prefix  bracket's prefix default is 'AND'(括号的前缀，默认式 AND)
     * @return
     */
    public function addLeftBrackets($prefix='AND')
    {
        $this->where->append(" {$prefix} (");
        return $this;
    }

    /**
     * add right brackets(添加右边括号)
     * @return
     */
    public function addRightBrackets()
    {
        $this->where->append(" ) ");
        return $this;
    }

    /**
     * 添加 IN 查询条件
     * @param $where
     * @return
     */
    public function addInWhere($field, $value) {

        $this->addJoinStr(" AND ");
        if ( is_array($value) ) {
            $this->where->append("{$field} IN('".explode("',", $value)."')");
        } else {
            $this->where->append("{$field} IN ({$value})");
        }
        return $this;
    }

    public function addOrInWhere($field, $value) {

        $this->addJoinStr(" OR ");
        if ( is_array($value) ) {
            $this->where->append("{$field} IN('".explode("',", $value)."')");
        } else {
            $this->where->append("{$field} IN ({$value})");
        }
        return $this;
    }

    //添加链接符号
    public function concat($str='AND') {
        if ( !$this->where->isEmpty() ) {
            $this->where->append($str);
        }
    }

    //关联表查询
    public function table($table_str) {
        $this->unionTable = $table_str;
        return $this;
    }

    /**
     * <p>直接设置查询条件字符串, 如果设置了这个条件，其他条件都将失效</p>
     * <p>set where condition direct, if set the condition, other condition will does not work</p>
     * @param $where
     * @return
     */
    public function where($where)
    {
        $this->whereString = $where;
        return $this;
    }

    public function pagesize($pagesize)
    {
        if ( $pagesize > 0 ) $this->pagesize = $pagesize;
        return $this;
    }

    public function field($field)
    {
        $this->fields = $field;
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
        $this->group = $group;
        return $this;
    }

    public function having($having)
    {
        $this->having = $having;
        return $this;
    }

    public function setTable($table)
    {
        $this->_table = $table;
        return $this;
    }

    private function getFieldValue($value) {
        if  ( is_numeric($value) ) {
            return $value;
        } else {
            return "'{$value}'";
        }
    }
}
