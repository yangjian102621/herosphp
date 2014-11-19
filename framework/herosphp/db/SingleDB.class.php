<?php
namespace modphp\db;

use modphp\db\interfaces\Idb;
use \PDO;
use \PDOException;
use modphp\debug\Debug;
/**
 * 单数居库服务器操作的PDO实现
 * @author      yangjian102621@gmail.com
 * @since       2014-06-06
 */

class SingleDB implements Idb {

    private $link;      /* PDO 数据库链接实例 */

    private $config = NULL;      /* 数据库配置参数 */

    /* 创建一个数据库操作对象 */
    public  function __construct( $config ) {
        $this->config = $config;
    }
    
    /**
     * connect database
     * @throws DBException
     * @return      resource of database connection.
     */
    public function connect()
    {
        if ( $this->link != NULL ) return TRUE;
        $_config = $this->config;
        $_dsn="{$_config['db_type']}:host={$_config['db_host']};dbname={$_config['db_name']}";
        try {
            $this->link = new PDO($_dsn, $_config['db_user'], $_config['db_pass'], array(PDO::ATTR_PERSISTENT=>true));
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //设置数据库编码，默认使用UTF8编码
            $_charset = $_config['db_charset'];
            if ( !$_charset ) $_charset = 'UTF8';
            $this->link->query("SET names {$_charset}");
            $this->link->query("SET character_set_client = {$_charset}");
            $this->link->query("SET character_set_results = {$_charset}");

        } catch (PDOException $e ) {
            $_exception = new DBException("数据库连接失败".$e->getMessage());
            $_exception->setCode($e->getCode());
            throw $_exception;
        }

        return $this->link;
    }

    /**
     * execute an SQL
     * @param        string $_query query string
     * @throws DBException
     * @return \PDOStatement
     */
    public function query($_query)
    {
        if ( $this->link == NULL ) $this->connect();
        try {
            $_result = $this->link->query($_query);
        } catch ( PDOException $e ) {
            $_exception = new DBException("SQL错误:" . $e->getMessage());
            $_exception->setCode($e->getCode());
            $_exception->setQuery($_query);
            throw $_exception;
        }
        return $_result;
    }

    /**
     * insert a record to database.
     * @param        string $_table table name
     * @param         array $_array
     * @return        int        return the last insert id
     */
    public function insert($_table, &$_array)
    {	
		$_fileds = '';
		$_values = '';
		$_T_fields = $this->getTableFields( $_table );
		foreach ( $_array as $_key => $_val ) {

			//自动过滤掉不存在的字段
			if ( !in_array( $_key, $_T_fields ) ) continue;

			$_fileds .= ( $_fileds=='' ) ? "`{$_key}`" : ", `{$_key}`" ;
			$_values .= ( $_values=='' ) ? "'".$_val."'" : ",'".$_val."'";
			
		}

		if ( $_fileds !== NULL ) {
			$_query = "INSERT INTO ".$_table."(" . $_fileds . ") VALUES(" . $_values . ")";			
			
			if ( $this->query( $_query ) != FALSE ){
				return $this->link->lastInsertId();
			}
		}		
        return FALSE;
    }

    /**
     * insert a record, if the record exists update it.
     * @param $_table
     * @param $_array
     * @return      boolean
     */
    public function replace($_table, &$_array ) {

        $_fileds = '';
        $_values = '';
        $_T_fields = $this->getTableFields( $_table );
        foreach ( $_array as $_key => $_val ) {

            //自动过滤掉不存在的字段
            if ( !in_array( $_key, $_T_fields ) ) continue;

            $_fileds .= ( $_fileds=='' ) ? "`{$_key}`" : ", `{$_key}`";
            $_values .= ( $_values=='' ) ? "'".$_val."'" : ",'".$_val."'";
        }

        if ( $_fileds !== NULL ) {
            $_query = "REPLACE INTO ".$_table."(" . $_fileds . ") VALUES(" . $_values . ")";
            if ( $this->query( $_query ) != FALSE )
                return TRUE;
        }
        return FALSE;
    }

    /**
     * delete a record from table.
     * @param        string $_table table name
     * @param        string $_conditons query condition.
     * @return        mixed
     */
    public function delete($_table, $_conditons = NULL)
    {
        $_sql = "DELETE FROM ".$_table;
        if ( $_conditons != NULL ) $_sql .= " WHERE ".$_conditons;
        $_result = $this->query($_sql);
        if ( $_result ) return true;
        return FALSE;
    }

    /**
     * Get a list of data records.
     * @param           $_query    the query string
     * @param int|\modphp\db\interfaces\type|\modphp\db\type $_type type of array to the result
     * @return          array
     */
    public function &getItems($_query, $_type = PDO::FETCH_ASSOC)
    {
        $_result = array();
        $_ret = $this->query( $_query );
        if ( $_ret != FALSE ) {

            while ( ($_rows = $_ret->fetch($_type)) != FALSE )
                $_result[]  = $_rows;
        }
        return $_result;
    }

    /**
     * get one data records
     * @param        string $_query query string
     * @param int|\modphp\db\interfaces\type|\modphp\db\type $_type type of array to the result
     * @return        array
     */
    public function &getItem($_query, $_type = PDO::FETCH_ASSOC)
    {
        $_result = array();
        $_ret = $this->query( $_query );
        if ( $_ret != FALSE ) {
            $_result = $_ret->fetch($_type);
        }
        return $_result;
    }

    /**
     * 更新一条记录
     * @param    string $_table table name
     * @param    array $_array data array  name => value
     * @param    string $_conditons query conditions.
     * @return    int
     */
    public function update($_table, &$_array, $_conditons)
    {
        $_T_fields = $this->getTableFields($_table);
        $_keys = '';
        foreach ( $_array as $_key => $_val ) {

            //过滤不存在的字段
            if ( !in_array($_key, $_T_fields) ) continue;
            $_keys .= $_keys == ''? "`{$_key}`='{$_val}'" : ", `{$_key}`='{$_val}'";
        }
        if ( $_keys !== '' ) {
            $_query = "UPDATE " . $_table . " SET " . $_keys . " WHERE ".$_conditons;
            if ( $this->query( $_query ) !== FALSE) return TRUE;
        }
        return FALSE;
    }

    /**
     * get total records rows number.(获取总记录数)
     * @param        string $_table table name
     * @param        string $_conditons query conditions
     */
    public function count($_table, $_conditons = NULL)
    {
        $_query = "SELECT count(*) as total FROM {$_table}";
        if ( $_conditons ) $_query .= " WHERE ".SQL::createCondition($_conditons);
        $_result = $this->query($_query);
        $_res = $_result->fetch(PDO::FETCH_ASSOC);
        return $_res['total'];
    }

    /**
     * begin transaction (事物开启)
     */
    public function beginTransaction()
    {
        if ( $this->link == NULL ) $this->connect();
        $this->link->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $this->link->beginTransaction();
    }

    /**
     * commit transaction (事物提交)
     */
    public function commit()
    {
        if ( $this->link == NULL ) $this->connect();
        $this->link->commit();
        $this->link->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    /**
     * roll back (事物回滚)
     */
    public function rollBack()
    {
        if ( $this->link == NULL ) $this->connect();
        $this->link->rollBack();
    }

    /***
     * 获取指定数据表的所有字段
     * @param		string 		$_table		table name
     * @return 		array		fields array of table
     */
    protected function getTableFields( $_table ) {

        $_sql = "SHOW COLUMNS FROM {$_table}";
        $_ret = $this->query( $_sql );
        $_fields = array();
        if ( $_ret != FALSE ) {
            while ( ($_rows = $_ret->fetch()) != FALSE ) {
                $_fields[] = $_rows[0];
            }
        }
        return $_fields;
    }

    /**
     * 释放资源
     */
    public function __destruct() {

        if ( $this->link ) $this->link = NULL;
    }
    
	/**
	 * @return the $config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param field_type $config
	 */
	public function setConfig($config) {
		$this->config = $config;
	}
}