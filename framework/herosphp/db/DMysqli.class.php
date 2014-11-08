<?php
/**
 * mysqli数据库操作类， 实现Idb.class.php 接口。
 * mysqli database operation class, implements class Idb.
 * 优势：mysql每次链接都会打开一个连接的进程而
 * mysqli多次运行mysqli将使用同一连接进程,从而减少了服务器的开销.
 * 本类自带文件缓存机制
 * --------------------------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * --------------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version 1.1 
 * @completed	2013-04-08
 */
 
class DMysqli implements Idb  {
	
	/* information of database connection */
	private $_db_info = array();
	
	/* link mysql connect resource */
	private $mysqli = NULL;
	
	/**
	 * constructor, initialization parameters.
	 * @param		array 		$_db_info;		information of database.
	 */
	public function __construct( &$_db_info ) {
		if ( $_db_info == NULL ) $_db_info = SysCfg::$db_info[0];
		$this->_db_info = $_db_info;
		if ( $this->mysqli == NULL ) $this->connect();
		//set charset of database
		$this->mysqli->set_charset($this->_db_info['charset']);
	}
	
	private function connect() {
		$this->mysqli = new MySQLi($this->_db_info['host'], $this->_db_info['user'],$this->_db_info['pass'], $this->_db_info['db'], $this->_db_info['port']);
		if ( mysqli_connect_errno() ) {
			die('错误：'.mysqli_connect_error());
		} 
	}
	
	/**
	 * execute an SQL
	 * @param		string  	$_sql		SQL
	 */
	public function query( $_sql ) {
		
		if ( trim($_sql) == "" ) return FALSE;
		if ( $this->mysqli == NULL ) $this->connect();
		if ( SysCfg::$debug ) Debug::appendMessage("query string : ".$_sql, 'sql');
		return $this->mysqli->query($_sql); 
		
	}
	
	/**
	 * insert a record to database.
	 * @param		string		$_table		table name
	 * @param		array		$_fields	data array  field => value
	 * @return		int 		insert_id	return the last insert id 
	 */
	public function insert( $_table, &$_array ) {
		
		$_fileds = NULL;$_values = NULL;
		$_T_fields = $this->getTableFields( $_table );
		foreach ( $_array as $_key => $_val ) {
			if ( in_array( $_key, $_T_fields ) ) {
				$_fileds .= ( $_fileds==NULL ) ? $_key : ',' . $_key;
				$_values .= ( $_values==NULL ) ? "'".$_val."'" : ",'".$_val."'";	
			}
		}
	
		if ( $_fileds !== NULL ) {
			$_query = "INSERT INTO ".$_table."(" . $_fileds . ") VALUES(" . $_values . ")";
			if ( $this->query( $_query ) != FALSE )
				return mysqli_insert_id($this->mysqli);
		}
		return FALSE;
		
	}
    /**
     * insert a record, if the record exists update it. 
     * @return      boolean
     */
    public function replace( $_table, &$_array ) {
    	
        $_fileds = NULL;$_values = NULL;
        $_T_fields = $this->getTableFields( $_table );
        foreach ( $_array as $_key => $_val ) {
            if ( in_array( $_key, $_T_fields ) ) {
                $_fileds .= ( $_fileds==NULL ) ? $_key : ',' . $_key;
                $_values .= ( $_values==NULL ) ? "'".$_val."'" : ",'".$_val."'";    
            }
        }
    
        if ( $_fileds !== NULL ) {
            $_query = "REPLACE INTO ".$_table."(" . $_fileds . ") VALUES(" . $_values . ")";
            if ( $this->query( $_query ) != FALSE )
                return TRUE;
        }
        return FALSE;
    }
	
	/***
	 * get fields of table
	 * @param		string 		$_table		table name
	 * @return 		array		fields array of table
	 */
	private function getTableFields( $_table = NULL ) {
		
		$_sql = "SHOW COLUMNS FROM {$_table}";
		$_ret = $this->query( $_sql );
		$_fields = array();
		if ( $_ret != FALSE ) {
			while ( ($_rows = $_ret->fetch_row()) != FALSE ) {
				$_fields[] = $_rows[0];
			}
		}
		return $_fields;
	}
	
	/**
	 * delete a record from table.
	 * @param		string		$_table  	table name
	 * @param		string		$_where		query condition.
	 * @return 		mixed		false for faild, retrun affacts rows if success.
	 */
	public function delete( $_table, $_where = NULL ) {
		
		$_sql = "DELETE FROM ".$_table;
		if ( $_where != NULL ) $_sql .= " WHERE ".$_where;
		if ( $this->query($_sql) != FALSE ) {
			return mysqli_affected_rows($this->mysqli);
		}
		return FALSE;
	}
	
	/**
	 * Get a list of data records.
	 * @param		string		$_sql
	 * @param		int 		$_type		type of array to the result(返回结果集的类型)
	 * 										默认返回关联数组
	 * @param    	int 		$_serial    cache serial(缓存分类标志)
	 */
	public function &getList( $_sql, $_type = MYSQLI_ASSOC ) {
		
		$_result = array();
		$_ret = $this->query( $_sql );
		if ( $_ret != FALSE ) {
			while ( ($_rows = $_ret->fetch_array($_type)) != FALSE ) $_result[]  = $_rows;
			//释放结果集
			$_ret->free();
		}
		return $_result;
	}
	
	/**
	 * get one data records
	 * @param	string		$_sql
	 * @param		int 		$_type		type of array to the result(返回结果集的类型)
	 * 										默认返回关联数组
	 * @param    	int 		$_serial    cache serial(缓存分类标志)
	 */
	public function &getOneRow( $_sql, $_serial = -1, $_type = MYSQLI_ASSOC ) {
		
		$_result = array();
		$_ret = $this->query( $_sql );
		if ( $_ret != FALSE ) {
			$_result = $_ret->fetch_array($_type);
			//释放结果集
			$_ret->free();
		}
		return $_result;
		
	}
	
	/**
	 * update a record from table
	 * @param	string		$_table		table name
	 * @param	array		$_array 	data array  name => value
	 * @param	string		$_where		query conditions. 
	 */
	public function update( $_table, &$_array, $_where ) {
		
		$_fileds = NULL;$_values = NULL;
		$_keys = NULL;
		foreach ( $_array as $_key => $_val ) {
			if ( $_keys == NULL ) 
				$_keys .= $_key.'=\''.$_val.'\'';
			else 
				$_keys .= ','.$_key.'=\''.$_val.'\'';
		}
		if ( $_keys !== NULL ) {
			$_query = "UPDATE " . $_table . " SET " . $_keys . " WHERE ".$_where;
			return $this->query( $_query );
		}
		return FALSE;
		
	}
	
	/**
	 * get total records rows number.
	 * @param		string 		$_table 	table name
	 * @param		string		$_fields	fields to query
	 * @param		string		$_where		query conditions
     * @return      int
	 */
	public function count( $_table, $_where ) {
		
		$_sql = "SELECT count(*) as total FROM {$_table}";
		if ( $_where ) $_sql .= " WHERE ".$_where;
        $_res = mysqli_fetch_assoc($this->query($_sql));
	    return $_res['total'];
		
	}
    
    /**
     * get affected rows  
     * @param       string          $_sql   query language
     */
    public function affectedRows() {
        return mysqli_affected_rows($this->mysqli);
    }
    
    /**
     * begin transaction (事物开启)
     */
    public function begin() {
        $this->mysqli->autocommit(false);
    }
    
    /**
     * commit transaction (事物提交)
     */
    public function commit() {
        $this->mysqli->commit();
        $this->mysqli->autocommit(true);

    }
    
   /**
    * roll back (事物回滚) 
    */
    public function rollBack() {
        $this->mysqli->rollback();
        $this->mysqli->autocommit(true);

    }
    /**
     * 获取数据库使用大小
     * 
     * @param   $_db_name    数据库名称
     * @return  string      返回转换后单位的尺寸
     */
    public function getDataSize( $_db_name = NULL ) {
    	
        if ( $_db_name == NULL ) $_db_name = $this->_db_info['db'];
        $_sql = "SHOW TABLE STATUS FROM " . $_db_name;
        $_result=$this->query($_sql);
        $_size = 0;
        while( $_row = $_result->fetch_assoc() )
            $_size += $_row["Data_length"] + $_row["Index_length"];
        return Utils::formatFileSize($_size);
		
    }
    /*
     * 数据库的版本
     * @return  string      返回数据库系统的版本
     */
    public function dbVersion() {
        return  mysqli_get_client_version($this->mysqli);
    }
	
	/* close the mysql connection. */
	public function __destruct() {
		$this->mysqli->close();	
	}
}
?>