<?php

namespace herosphp\db\mysql;

/*---------------------------------------------------------------------
 * mysql查询语句处理工具，用来将通用api传入的查询条件转换成mysql的查询条件
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

class MysqlQueryBuilder {

    private $table; //数据表

    private $fields = '*';  //查询字段

    private $where = array(); //查询条件

    private $order = ''; //排序方式

    private $group = ''; //分组方式

    private $having = array(); //分组条件

    private $limit = ''; //查询limit

    /**
     * 字段比较操作符
     * @var array
     */
    private static  $operator = array(
        '>', '<', '>=', '<=', '!='
    );

    private function __construct() {}

    //创建实例
    public static function getInstance() {
        return new self();;
    }

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $fields 推荐格式：array('id','name','pass')
     * @return $this
     */
    public function fields($fields) {

        if ( !$fields ) {
            $this->fields = '*';
            return $this;
        }
        if ( is_array($fields) ) {
            $this->fields = '`'.implode("`, `", $fields).'`';
        } else if ( is_string($fields) ) {
            $this->fields = $fields;
        }
        return $this;
    }

    public function where($conditions) {
        if ( is_array($conditions) ) $this->where = $conditions;
        return $this;
    }

    /**
     * 设置分组
     * @param  string $groupKey 分组字段
     * @return $this
     */
    public function group($groupKey) {
        $this->group = $groupKey;
        return $this;
    }

    /**
     * 设置分组条件( build having string )
     * @param array $having
     * @return $this
     */
    public function having($having) {
        if ( is_array($having) ) $this->having = $having;
        return $this;
    }

    /**
     * 处理排序
     * @param array $order array('id' => 1, 'addtime' => -1)
     * @return $this
     */
    public function order($order) {

        if ( is_string($order) ) {
            $this->order = $order;
            return $this;
        }
        if( is_array($order) ) {
            $__order = array();
            foreach ( $order as $key => $value ) {
                if ( $value == 1 ) {
                    $__order[] = "{$key} ASC";
                } else if ( $value == -1 ) {
                    $__order[] = "{$key} DESC";
                }
            }
            $this->order = implode(',', $__order);
        }
        return $this;
    }

    /**
     * 设置查询偏移
     * @param array $limit 标准格式:array($page, $size)
     * @return $this
     */
    public function limit($limit) {
        $this->limit = self::parseLimit($limit);
        return $this;
    }

    //解析limit字符串
    public static function parseLimit($limit) {
        //1. limit(10);
        if ( is_numeric($limit) ) {
            return "0, {$limit}";
        }
        //2. limit("10, 50")
        if ( is_string( $limit ) ) {
            return $limit;
        }
        //3. limit(array(10, 20))
        if ( is_array($limit) ) {
            $limit[0] = ($limit[0] - 1) * $limit[1];
            return implode(',', $limit);
        }
        //推荐列表查询一定是要分页的，如果没有分页则显示前20页
        return '0, 20';
    }

    /**
     * @param $limit array($page, $pagesize)
     * @return array
     */
    public static function parseLimitAsArray($limit) {
        $offset = 0;
        $pagesize = 20;
        //1. limit(10);
        if ( is_numeric($limit) ) {
            $offset = 0;
            $pagesize = $limit;
        }
        //2. limit("10, 50")
        if ( is_string( $limit ) ) {
            $limit = explode(',', $limit);
        }
        //3. limit(array(10, 20))
        if ( is_array($limit) ) {
            $offset = ($limit[0] - 1) * $limit[1];
            $pagesize = $limit[1];
        }
        return array($offset, $pagesize);
    }

    /**
     * 组合查询条件
     * @param $where 条件数组
     * @param $add_brackets 是否在逻辑运算之间添加括号
     * @return string
     */
    public static function buildConditions($where=null,$add_brackets=true) {

        if ( !$where || empty($where) ) return '1';

        /**
         * 基于 key => value 数组语法的查询条件解析,这里借鉴的是mongodb的查询语法，以便兼容mongodb
         * array('name' => 'zhangsan', '|age' => array('>' => 24, '<' => 30))
         */
        $condi = array(" 1 ");
        foreach ( $where as $key => $value ) {
            /**
             * 组合条件
             * array('name' => 'zhangsan', '$or' => array('name' => 'lisi', 'age'=>12))
             */
            if ( strpos($key, '$or') !== false ) {
                $condi[] = ' OR (';
                $condi[] = self::buildConditions($value, false);
                $condi[] = ')';
                continue;
            }
            //这里判断是AND,OR还是取反逻辑、
            switch ( $key[0] ) {
                case '|':
                    $condi[] = ' OR ';
                    $key = substr($key, 1);
                    break;
                case '!':
                    $condi[] = ' AND !';
                    $key = substr($key, 1);
                    break;
                case '#':
                    $condi[] = ' OR !';
                    $key = substr($key, 1);
                    break;
                case '&':
                default :
                    $condi[] = ' AND ';
            }
            if ( $add_brackets ) {
                $condi[] = '('; //两个逻辑条件之间用括号括起来，以便于逻辑清晰
            }
            //1. 普通的等于查询 array('name' => 'xiaoming');
            if ( !is_array($value) ) {
                $condi[] = "`{$key}` ".self::getFormatValue($value);
                if ( $add_brackets ) {
                    $condi[] = ')';
                }
                continue;
            }

            if ( is_array($value) ) {

                $subCondi = array();
                foreach ( $value as $key1 => $value1 ) {
                    //2. 操作符查询 array('age' => array('>' => 24, '<=' => 30))
                    if ( in_array($key1, self::$operator) ) {
                        $subCondi[] = "`{$key}` {$key1} '{$value1}'";
                        continue;
                    }
                    /**
                     * 3. IN not in查询,支持2种形式
                     * array('id' => array('$in' => array(1,2,3)))
                     * array('id' => array('$in' => '1,2,3'))
                     */
                    if ( $key1 == '$in' || $key1 == '$nin' ) {
                        if ( is_array($value1) ) {
                            $value1 = implode("','", $value1);
                            $value1 = "'{$value1}'";
                        }
                        $subCondi[] = $key1 == '$in' ? "`$key` IN ({$value1})" : "`$key` NOT IN ({$value1})";
                        continue;
                    }

                    /**
                     * 5. null查询,数据库中没有初始化的数据默认值为null, 此时不能用 name='' 或者name='null'查询
                     * array('name' => array('$null' => 1|-1)) 1 => null, -1 => not null
                     */
                    if ( $key1 == '$null' ) {
                        if ( $value1 == 1 ) {
                            $subCondi[] = "`{$key}` is null";
                        } elseif( $value1 == -1 ) {
                            $subCondi[] = "`{$key}` is not null";
                        }
                    }
                }//end foreach

                if ( !empty($subCondi) ) {
                    $condi[] = implode(' AND ', $subCondi);
                }

            }//end if

            if ( $add_brackets ) {
                $condi[] = ')';
            }

        } //end foreach
        return implode(' ', $condi);
    }


    /**
     * 获取正确格式的字段值
     * @param $value
     * @return string
     */
    public static function getFormatValue($value) {

        //1. 包含操作符的
        $opt = substr($value, 0, 2);
        if ( in_array($value[0], self::$operator) && !in_array($opt, self::$operator) ) {
            //获取真正的value
            $_value = substr($value, 1);
            if ( is_numeric($_value) ) {
                return "{$value[0]} {$_value}";
            } else {
                return "{$value[0]} '{$_value}'";
            }
        }
        if ( in_array($opt, self::$operator) ) {
            //获取真正的value
            $_value = substr($value, 2);
            if ( is_numeric($_value) ) {
                return "{$opt} {$_value}";;
            } else {
                return "{$opt} '{$_value}'";
            }
        }

        //2.like查询 array('title' => '%abc%')
        if ( $value[0] == '%' || $value[strlen($value) - 1] == '%' ) {
            return "LIKE '{$value}'";
        }

        return is_numeric($value) ? "={$value}" : "='{$value}'";
    }

    /**
     * 创建SQL语句
     * @return string
     * @throws \herosphp\exception\HeroException
     */
    public function buildQueryString() {

        if ( $this->table == '' ) E("请在model中指定数据表.");

        $query = "SELECT {$this->fields} FROM ".$this->table;

        if ( $this->where ) $query .= " WHERE " .self::buildConditions($this->where);
        if ( $this->group ) $query .= " GROUP BY ".$this->group;
        if ( $this->having ) $query .= " HAVING ".self::buildConditions($this->having);
        if ( $this->order ) $query .= " ORDER BY ".$this->order;
        if ( $this->limit ) $query .= " LIMIT ".$this->limit;

        return $query;
    }

}
