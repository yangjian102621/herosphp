<?php
/**
 * mysql查询语句处理工具，用来将通用api传入的查询条件转换成mysql的查询条件
 * ---------------------------------------------------------------------
 * @author yangjian<yangjian102621@gmail.com>
 * @since 2013-05 v1.0.0
 *-----------------------------------------------------------------------*/
namespace herosphp\db\utils;

use herosphp\exception\HeroException;
use herosphp\string\StringBuffer;

class MysqlQueryBuilder {

    private $table; //数据表

    private $fields = '*';  //查询字段

    /**
     * 查询条件
     * @var StringBuffer
     */
    private $condition;
    private $order = null; //排序方式
    private $group = null; //分组方式
    /**
     * 分组条件
     * @var StringBuffer
     */
    private $havingCondition;
    private $limit = '0,10'; //查询limit
    private $closure = false; //是否进入闭包
    /**
     * 简单条件操作符
     * @var array
     */
    protected static $SIMPLE_OPTS = ['=','>', '<', '>=', '<=', '!='];
    /**
     * 复杂条件操作符
     * @var array
     */
    protected static $COMPLEX_OPTS = ['IN', 'NIN', 'NOT', 'BETWEEN', 'LIKE','NULL','NNULL'];

    public function __construct($table) {
        $this->table = $table;
        $this->condition = new StringBuffer();
        $this->havingCondition = new StringBuffer();
    }

    /**
     * 设置查询字段
     * @param array $fields 推荐格式：array('id','name','pass')
     * @return $this
     */
    public function fields($fields) {

        if ( is_array($fields) ) {
            $this->fields = '`'.implode("`, `", $fields).'`';
        } else if ( is_string($fields) ) {
            $this->fields = $fields;
        } else {
            $this->fields = '*';
        }
        return $this;
    }

    /**
     * @param $field
     * @param $opt
     * @param $value
     * @param $logic
     */
    public function addWhere($field, $opt=null, $value=null, $logic='AND') {

        //如果只有一个参数，则说明追加字符串
        if ( func_num_args() == 1 ) {
            $this->condition->append($field);
            return;
        }

        $whereStr = $this->parseWhere($field, $opt, $value);
        if ( $whereStr == '' ) return;

        if ( $this->condition->isEmpty() ) {
            if ( $this->closure ) {
                $this->condition->append(" WHERE ({$whereStr}");
            } else {
                $this->condition->append(" WHERE {$whereStr}");
            }
        } else {
            if ( $this->closure ) {
                $this->condition->append(" {$logic} ({$whereStr}");
                $this->outClosure();
            } else {
                $this->condition->append(" {$logic} {$whereStr}");
            }
        }
    }

    /**
     * 解析查询条件
     * @param string $field
     * @param string $opt
     * @param mixed $value
     * @return string
     */
    public function parseWhere($field, $opt, $value) {
        if (in_array($opt, self::$SIMPLE_OPTS)) {
            return "{$field} {$opt} '{$value}'";
        }
        $opt = strtoupper($opt);
        $whereStr = '';
        switch ($opt) {
            case 'IN':
            case 'NIN':
                if ( is_array($value) ) {
                    foreach ($value as $key => $val) {
                        if (is_string($val)) {
                            $value[$key] = "'{$val}'"; //如果是字符串，则需要加上引号
                        }
                    }
                    $value = implode(',', $value);
                }
                if ($opt == 'IN') {
                    $whereStr = "{$field} IN({$value})";
                } else {
                    $whereStr = "{$field} NOT IN({$value})";
                }
                break;

            case 'NOT':
                $whereStr = "NOT {$field} {$opt} '{$value}'";
                break;

            case 'BETWEEN';
                foreach ($value as $key => $val) {
                    if ( is_string($val) ) {
                        $value[$key] = "'{$val}'";
                    }
                }
                $whereStr = "{$field} BETWEEN {$value[0]} AND {$value[1]}";
                break;

            case 'LIKE':
                $whereStr = "{$field} LIKE '{$value}'";
                break;

            case 'NULL':
                $whereStr = "{$field} IS NULL";
                break;

            case 'NNULL':
                $whereStr = "{$field} IS NOT NULL";
                break;

        }
        return $whereStr;

    }

    /**
     * 设置分组
     * @param  string $field 分组字段
     * @return $this
     */
    public function group($field) {
        $this->group = $field;
        return $this;
    }

    /**
     * @param $field
     * @param $opt
     * @param $value
     * @param $logic
     */
    public function addHaving($field, $opt, $value, $logic='AND') {

        if ( func_num_args() == 1 ) {
            $this->havingCondition->append($field);
            return;
        }

        $whereStr = $this->parseWhere($field, $opt, $value);
        if ( $whereStr == '' ) return;

        if ( $this->havingCondition->isEmpty() ) {
            if ( $this->closure ) {
                $this->havingCondition->append(" HAVING ({$whereStr}");
                $this->outClosure();
            } else {
                $this->havingCondition->append(" HAVING {$whereStr}");
            }
        } else {
            if ( $this->closure ) {
                $this->havingCondition->append(" ({$whereStr}");
                $this->outClosure();
            } else {
                $this->havingCondition->append(" {$logic} {$whereStr}");
            }
        }
    }

    /**
     * 往分组条件中追加字符串，主要是追加括号之类的
     * @param $str
     * @return $this
     */
    public function havingAppend($str) {

        return $this;
    }

    /**
     * 处理排序
     * @param string $order
     * @return $this
     */
    public function order($order) {
        $this->order = $order;
        return $this;
    }

    /**
     * 设置查询偏移
     * @param array $limit 标准格式:array($page, $size)
     * @return $this
     */
    public function limit($limit) {
        if ( $limit ) {
            $this->limit = $limit;
        } else {
            //推荐列表查询一定是要分页的，如果没有分页则显示前1000条
            $this->limit = '0, 1000';
        }
        return $this;
    }

    /**
     * 创建SQL语句
     * @return string
     * @throws HeroException
     */
    public function buildQueryString() {

        if ( $this->table == '' ) E("请在model中指定数据表.");

        $query = "SELECT {$this->fields} FROM ".$this->table;

        if ( !$this->condition->isEmpty() ) $query .= $this->condition->toString();
        if ( $this->group ) $query .= " GROUP BY ".$this->group;
        if ( !$this->havingCondition->isEmpty() ) $query .= $this->havingCondition->toString();
        if ( $this->order ) $query .= " ORDER BY ".$this->order;
        if ( $this->limit ) $query .= " LIMIT ".$this->limit;

        $this->clear(); //初始化查询条件
        return $query;
    }

    /**
     * 创建查询语句
     * @return string
     */
    public function buildCondition() {
        $conditions = $this->condition->toString();
        $this->clear(); //初始化查询条件
        return $conditions;
    }

    /**
     * 进入闭包
     */
    public function enterClosure() {
        $this->closure = true;
    }

    /**
     * 跳出闭包
     */
    public function outClosure() {
        $this->closure = false;
    }

    /**
     * 清空条件
     */
    public function clear() {
        $this->condition = null;
        $this->havingCondition = null;
        $this->group = null;
        $this->fields = "*";
        $this->order = null;
        $this->limit = "0,10";
    }

}
