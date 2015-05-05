<?php

namespace herosphp\db;

/*---------------------------------------------------------------------
 * 创建SQL查询语句
 * SELECT id,name,count(*) as total FROM table WHERE 1 GROUP BY pid
 * HAVING total > 5 ORDER BY total desc LIMIT 1, 100
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

class SQL {

    /**
     * 数据表
     * @var string
     */
    private $table;

    /**
     * 查询字段
     * @var string | array
     */
    private $fields = '*';

    /**
     * 查询条件
     * @var string | array
     */
    private $where;

    /**
     * 排序方式
     * @var string | array
     */
    private $order;

    /**
     * 分组方式
     * @var string
     */
    private $group;

    /**
     * 分组条件
     * @var string
     */
    private $having;

    /**
     * 查询偏移量
     * @var string | array
     */
    private $limit;

    /**
     * 数据表的主键
     * @var string
     */
    private $priKey = 'id';

    /**
     * 字段比较操作符
     * @var array
     */
    private static  $operator = array(
        '>', '<', '>=', '<=', '!='
    );

    /**
     * SQL唯一实例
     * @var SQL
     */
    private static $instance = null;

    private function __construct( $priKey = null ) {
        if ( $priKey != null ) $this->priKey = $priKey;
    }

    /**
     * 创建实例
     * @param string $priKey  数据表主键
     * @return SQL
     */
    public static function create( $priKey = null ) {
        return new self($priKey);
    }

    /**
     * 初始化数据表
     * @param null $table
     * @return $this
     */
    public function table( $table = null ) {
        if ( $table != null ) $this->table = $table;
        return $this;
    }

    /**
     * 获取查询字段
     * @param $fields
     * @return $this
     */
    public function fields( $fields = null ) {
        if ( $fields ) {
            if ( is_array($fields) ) {
                $this->fields = implode(',', $fields);
            } else {
                $this->fields = $fields;
            }
        }
        return $this;
    }

    /**
     * @param $where
     * @return $this
     */
    public function where( $where ) {
        $this->where = $this->buildConditions($where);
        return $this;
    }

    /**
     * 组合查询条件
     * @param $where
     * @return null|string
     */
    public function buildConditions( $where ) {

        if ( !$where ) return null;
        if ( is_numeric($where) ) return "{$this->priKey}={$where}";
        if ( is_string($where) ) return $where;
        //数组条件
        if ( is_array($where) ) {
            //1. array(1, 2, 3)
            if ( is_numeric($where[0]) ) {
                return " {$this->priKey} in(".implode(',', $where).")";
            }
            //2. array('name' => 'zhangsan', '|age' => '>24')
            $condi = array(" 1 ");
            foreach ( $where as $key => $value ) {
                if ( $key[0] == '|' ) {
                    $condi[] = "OR";
                    $key = substr($key, 1);
                } else {
                    $condi[] = "AND";
                }
                $condi[] = "{$key} ".self::getFormatValue($value);
            }
            return implode(' ', $condi);
        }
    }

    /**
     * 获取正确格式的字段值
     * @param $value
     * @return string
     */
    public static function getFormatValue( $value ) {

        //1. 包含操作符的
        $opt = substr($value, 0, 2);
        if ( in_array($value[0], self::$operator)
            || in_array($opt, self::$operator)) {
            return $value;
        }
        //2. null, !null
        if ( $value === 'null' ) return "is null";
        if ( $value === '!null' ) return "is not null";

        //3. %value, %value%, value%
        if ( $value[0] == '%' || $value[strlen($value)-1] == '%' ) {
            return "LIKE '{$value}'";
        }

        return "='{$value}'";
    }

    /**
     * 设置分组
     * @param  $group
     * @return $this
     */
    public function group( $group = null ) {
        if ( $group ) $this->group = $group;
        return $this;
    }

    /**
     * 设置分组条件( build having string )
     * @param $having
     * @return $this
     */
    public function having( $having ) {
        if ( is_string($having) ) {
            $this->having = $having;
        } else if ( is_array($having) ) {
            foreach ( $having as $key => $value ) {
                $this->having .= $this->having  ? "{$key} $value" : ", {$key} {$value}";
            }
        }
        return $this;
    }

    /**
     * 处理排序
     * @param $order
     * @return $this
     */
    public function order( $order = null ) {
        if ( $order ) {
            $orderWays = array();
            //1. array('id' => 'desc', 'hits' => 'desc')
            if ( is_array( $order ) ) {
                foreach ( $order as $key => $value ) {
                    $orderWays[] = "{$key} {$value}";
                }
                $this->order = implode(',', $orderWays);

                //2. id desc, name asc
            } else {
                $this->order = $order;
            }
        }
        return $this;
    }

    /**
     * 设置查询偏移
     * @param $limit
     * @return $this
     */
    public function limit( $limit ) {
        //1. limit(10);
        if ( is_numeric($limit) ) {
            $this->limit = "0, {$limit}";

            //2. limit("10, 50")
        } else if ( is_string( $limit ) ) {
            $this->limit = $limit;

            //3. limit(array(10, 20))
        } else if ( is_array($limit) ) {
            $this->limit = implode(',', $limit);
        }
        return $this;
    }

    /**
     * 创建SQL语句
     * @return string
     * @throws \herosphp\exception\HeroException
     */
    public function buildQueryString() {
        if ( !$this->table ) {
            E("找不到数据表!");
        }
        $query = "SELECT {$this->fields} FROM ".$this->table;

        if ( $this->where ) $query .= " WHERE " .$this->where;
        if ( $this->group ) $query .= " GROUP BY ".$this->group;
        if ( $this->having ) $query .= " HAVING ".$this->having;
        if ( $this->order ) $query .= " ORDER BY ".$this->order;
        if ( $this->limit ) $query .= " LIMIT ".$this->limit;

        return $query;
    }

}

?>
