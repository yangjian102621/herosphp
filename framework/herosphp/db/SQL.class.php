<?php
/*---------------------------------------------------------------------
 * 创建SQL查询语句
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/
 
class SQL {
    
    private static $_instance = NULL;
    
    private $_query = '';
    
    //primary key
    private $_pri = 'id';
    
    //table to deal with
    private $_table = NULL;
    
    //fields to query
    private $_fields = NULL;
    
    //conditions of query string
    private $_where = NULL;
    
    //order way 
    private $_order = NULL;
    
    //group way
    private $_group = NULL;
    
    //having conditions
    private $_having = NULL;
    
    //limit of query records
    private $_limit = NULL;
        
    //constructor
    private function construct() {}
    
    /**
     * create sql
     * @return      SQL              
     */
    public static function create() {
        if ( self::$_instance == NULL ) 
            self::$_instance = new self();
        return self::$_instance;
    }

    /**
     * query fields
     * @param       string | array $_fields 查询字段
     * @return $this
     */
    public function fields( $_fields ) {
        if ( !$_fields ) $_fields = '*';
        $this->_fields = $_fields;
        return $this;
    }

    /**
     * query conditions
     * @param       mixed(int, array, string) $_where
     * @return $this
     */
    public function where( $_where ) {
        $this->_where = func_get_args();
        return $this;
    }

    /**
     * order way
     * @param       string | array $_order
     * @return $this
     */
    public function order( $_order ) {
        if ( $_order ) $this->_order = $_order;
        return $this;
    }

    /**
     * group by and group way
     * @param       string | array $_group
     * @return $this
     */
    public function group( $_group ) {
        if ( $_group ) $this->_group = $_group;
        return $this;
    }

    /**
     * group conditions
     * @param       string | array $_having
     * @return $this
     */
    public function having( $_having ) {
        if ( $_having  ) $this->_having = $_having;
        return $this;
    }
    
    /**
     * limit of query result
     * @param       string | array         $_limit 
     */
    public function limit( $_limit ) {
        if ( $_limit ) $this->_limit = $_limit;
        return $this;
    }

    /**
     * name of data table
     * @param       string $_table
     * @return  $this
     */
    public function table( $_table ) {
        if ( $_table ) $this->_table = $_table;
        return $this;
    }
    
    //create qurey string
    public function getSQL() {
        
        if ( !$this->_table ) {
            Debug::appendMessage("找不到数据表！", "sql");
            return '';
        }
        
        $this->_query = "SELECT ";
        
        //bulid fields
        if ( $this->_fields == NULL ) $this->_fields();
        if ( is_string($this->_fields) ) {
            $this->_query .= $this->_fields;
        } else if ( is_array($this->_fields) ) {
            $this->_query .= implode(',', $this->_fields);
        }
        
        $this->_query .= " FROM ".$this->_table;
        
        //build conditions(组合查询条件)
        $_conditions = $this->buildConditions();
        if ( $_conditions != '' ) $this->_query .= " WHERE {$_conditions}";
        
        //build group by string 处理分组
        $_group = '';
        if ( is_string($this->_group) ) {
            $_group = $this->_group;
        } else if ( is_array($this->_group) ) {
            foreach ( $this->_group as $_name => $_val ) {
                $_group .= $_group == '' ? "{$_name} $_val" : ", {$_name} {$_val}";
            }
        }
        if ( $_group != '' ) $this->_query .= " GROUP BY {$_group}";
        
        //build having string
        $_having = '';
        if ( is_string($this->_having) ) {
            $_having = $this->_having;
        } else if ( is_array($this->_having) ) {
            foreach ( $this->_having as $_name => $_val ) {
                $_having .= $_having == '' ? "{$_name} $_val" : ", {$_name} {$_val}";
            }
        }
        if ( $_having != '' ) $this->_query .= " HAVING {$_having}";
        
        //build order by string 处理排序
        $_order = '';
        if ( is_string($this->_order) ) {
            $_order = $this->_order;
        } else if ( is_array($this->_order) ) {
            foreach ( $this->_order as $_name => $_val ) {
                $_order .= $_order == '' ? "{$_name} $_val" : ", {$_name} {$_val}";
            }
        }
        if ( $_order != '' ) $this->_query .= " ORDER BY {$_order}";
        
        //build limit string
        $_limit = '';
        //1. limit(10);
        if ( is_numeric($this->_limit) ) {
            $_limit = "0, {$this->_limit}"; 
            
            //2. limit("10, 50")
        } else if ( is_string( $this->_limit ) ) {
            $_limit = $this->_limit;
            
            //3. limit(array(10, 20))
        } else if ( is_array($this->_limit) ) {
            $_limit = implode(',', $this->_limit);
        }
        if ( $_limit != '' ) $this->_query .= " LIMIT {$_limit}";
        
        $_query = $this->_query;
        $this->sqlReset();
        return $_query;

    }
    
    //build conditions
    public function buildConditions() {

        if ( !is_array($this->_where) ) return '';
        $_conditions = '';
        foreach ( $this->_where as $_value ) {
            if ( is_string($_value) ) {
                //1. id 字符串
                $_args = explode(',', $_value);
                if ( is_numeric($_args[0]) ) {
                    if ( count($_args) == 1 ) {
                    	$_conditions .= "{$this->_pri}={$_value} ";
                    } else {
                    	$_conditions .= "{$this->_pri} IN ({$_value}) ";
                    }
                } else {
                    $_conditions .= $_value;
                }
            } else if ( is_numeric($_value) ) {
                //2. 传入的 是id筛选
                $_conditions .= "{$this->_pri} = {$_value} ";
            } else if ( is_array($_value) ) {
                //3. 一维数组如 array(1,2,3,4)
                if ( isset($_value[0]) ) {
                    $_conditions .= "{$this->_pri} IN (".implode(',', $_value).")";
                    $_conditions .= " OR ";
                    continue;
                }
                
                //如果传入的是 key => value 数组
                foreach ( $_value as $_name => $_val ) {
                    if ( is_string($_val) ) $_val = trim($_val);
                    //5. 如果是二维数组如：array('id' => array(1,2,3,4))
                    if ( is_array($_val) ) {
                        $_conditions .= "{$this->_pri} IN (".implode(',', $_val).")";
                        
                        //6.条件中包含>,<, >=, <=等符号的如：array('id >=' => 12)
                    } else if ( strpos($_name, ' ') !== FALSE ) {
                        $_conditions .= "{$_name} {$_val}";
                        
                        //7.条件中有模糊搜索的如： array( 'name' => '%zhangsan%' )
                    } else if ( $_val[0] == '%' && substr($_val, -1) == '%' ) {
                        $_conditions .= "{$_name} LIKE '{$_val}'";
                        
                        //8. 普通名值等于的形式如：array('name' => 'zhangsan', 'sex' => 'man')
                    } else {
                        $_conditions .= "{$_name}={$_val}";
                    }
                    
                    //逻辑与 AND
                    $_conditions .= " AND ";
                }
            }
            
            $_conditions = rtrim($_conditions, " AND ");
            //逻辑或 OR
            $_conditions .= " OR ";
        }
        $_conditions = trim(rtrim($_conditions, " OR "));
        
        return $_conditions;
    }

    //reset sql conditions
    private function sqlReset() {
        $this->_query = '';
        $this->_where = NULL;
        $this->_table = NULL;
        $this->_order = NULL;
        $this->_fields = NULL;
        $this->_group = NULL;
        $this->_having = NULL;
        $this->_limit = NULL;
    }
    
}

?>
