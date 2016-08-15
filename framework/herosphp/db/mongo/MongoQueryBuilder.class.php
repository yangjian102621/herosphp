<?php

namespace herosphp\db\mongo;

/*---------------------------------------------------------------------
 * mongodb查询语句处理工具，用来将通用api传入的查询条件转换成mongodb的查询条件
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

class MongoQueryBuilder {

    /**
     * mongodb条件操作符
     * @var array
     */
    private static  $operator = array(
        '>' => '$gt',
        '<' => '$lt',
        '>=' => '$gte',
        '<=' => '$lte',
        '!=' => '$ne'
    );

    private function __construct() {}

    public function table($table) {
        $this->table = $table;
        return $this;
    }

    /**
     * 组合查询字段
     * @param array $fields 推荐格式：array('id','name','pass')
     * @return $this
     */
    public static function fields($fields) {

        $arr = array();
        if ( is_string($fields) ) {
            $fields = explode(',', $fields);
        }
        foreach ( $fields as $key => $value ) {
            $arr[$value] = 1;
        }
        return $arr;
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
    public function having(array $having) {
        if ( is_array($having) ) $this->having = $having;
        return $this;
    }

    /**
     * 处理排序
     * @param array $order array('id' => 1, 'addtime' => -1)
     * @return $this
     */
    public function order($order) {
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
     * @param array $limit 标准格式:array($skip, $size)
     * @return $this
     */
    public function limit($limit) {
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
     * 组合查询条件
     * @param
     * @return string
     */
    public function where($where=null) {

        if ( !$where || empty($where) ) return array();

        /**
         * 基于 key => value 数组语法的查询条件解析,这里借鉴的是mongodb的查询语法，以便兼容mongodb
         * array('name' => 'zhangsan', '|age' => array('>' => 24, '<' => 30))
         * 转换后：array('name' => 'zhangsan', '$or' => array('age' => array('$lt' => 24, '$gt' => 30)))
         */
        $condi = array();
        foreach ( $where as $key => $value ) {
            //这里判断是AND,OR还是取反逻辑、
            switch ( $key[0] ) {
                case '|':
                    $condi['$or'] = array();
                    break;
                case '!&':
                    $condi[$key] = $value;
                    break;
                case '!|':
                    $condi['$not'] = ' OR !';
                    $key = substr($key, 2);
                    break;
                case '&':
                default :
                    $condi[] = ' AND ';
            }
            $condi[] = '('; //两个逻辑条件之间用括号括起来，以便于逻辑清晰
            //1. 普通的等于查询 array('name' => 'xiaoming');
            if ( !is_array($value) ) {
                $condi[] = "`{$key}` = ".self::getFieldValue($value);
                $condi[] = ')';
                continue;
            }

            $subCondi = array();
            foreach ( $value as $key1 => $value1 ) {
                //2. 操作符查询 array('age' => array('>' => 24, '<=' => 30))
                if ( in_array($key1, self::$operator) ) {
                    $subCondi[] = "`{$key}` {$key1} ".self::getFieldValue($value1);
                    continue;
                }
                /**
                 * 3. IN 查询,支持2种形式
                 * array('id' => array('in' => array(1,2,3)))
                 * array('id' => array('in' => '1,2,3'))
                 */
                $key1 = strtoupper($key1);
                if ( $key1 == 'IN' ) {
                    if ( is_array($value1) ) {
                        $value1 = implode("','", $value1);
                        $value1 = "'{$value1}'";
                    }
                    $subCondi[] = "`$key` IN ({$value1})";
                    continue;
                }

                //4. like查询 array('title' => array('like' => '%abc%'))
                if ( $key1 == 'LIKE' ) {
                    $subCondi[] = "`{$key}` LIKE '{$value1}'";
                    continue;
                }

                /**
                 * 5. null查询,数据库中没有初始化的数据默认值为null, 此时不能用 name='' 或者name='null'查询
                 * array('name' => array('null' => 1|-1)) 1 => null, -1 => not null
                 */
                if ( $key1 == 'NULL' ) {
                    if ( $value1 == 1 ) {
                        $subCondi[] = "`{$key}` is null";
                    } elseif( $value1 == -1 ) {
                        $subCondi[] = "`{$key}` is not null";
                    }
                }
            }
            if ( !empty($subCondi) ) {
                $condi[] = implode(' AND ', $subCondi);
            }

            $condi[] = ')';
        }
        return implode(' ', $condi);
    }

    /**
     * 获取正确格式的字段值
     * @param $value
     * @return string
     */
    public static function getFieldValue( $value ) {
        return is_numeric($value) ? $value : "'{$value}'";
    }

    /**
     * 创建SQL语句
     * @return string
     * @throws \herosphp\exception\HeroException
     */
    public function buildQueryString() {

        if ( $this->table == '' ) E("请在model中指定数据表.");

        $query = "SELECT {$this->fields} FROM ".$this->table;

        if ( $this->where ) $query .= " WHERE " .$this->buildConditions($this->where);
        if ( $this->group ) $query .= " GROUP BY ".$this->group;
        if ( $this->having ) $query .= " HAVING ".$this->buildConditions($this->having);
        if ( $this->order ) $query .= " ORDER BY ".$this->order;
        if ( $this->limit ) $query .= " LIMIT ".$this->limit;

        return $query;
    }

}

?>
