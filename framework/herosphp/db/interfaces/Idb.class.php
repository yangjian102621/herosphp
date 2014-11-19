<?php
namespace modphp\db\interfaces;

/**
 * 数据库操作通用接口，所有数据操作类必须实现这一接口。
 * database operate class common interface.
 * ----------------------------------------------------------
 * @author 	    yangjian<yangjian102621@gmail.com>
 * @version     1.0
 * @since	    2014-06-06
 */
interface Idb {

    /**
     * connect database
     * @return      resource of database connection.
     */
    public  function connect();

    /**
     * execute an SQL
     * @param		string  	$_query		query string
     * @return \PDOStatement
     */
    public function query( $_query );

    /**
     * insert a record to database.
     * @param        string      $_table table name
     * @param         array      $_array
     * @return        int        return the last insert id
     */
    public function insert( $_table, &$_array);

    /**
     * insert a record, if the record exists update it.
     * @param $_table
     * @param $_array
     * @return      boolean
     */
    public function replace($_table, &$_array );

    /**
     * delete a record from table.
     * @param		string		$_table  	table name
     * @param		string		$_conditons		query condition.
     * @return 		boolean
     */
    public function delete( $_table, $_conditons = NULL );

    /**
     * Get a list of data records.
     * @param           $_query    the query string
     * @param int|type $_type   type of array to the result
     * @return          array
     */
    public function &getItems( $_query, $_type = MYSQL_ASSOC );

    /**
     * get one data records
     * @param        string $_query        query string
     * @param       int|type $_type      type of array to the result
     * @return        array
     */
    public function &getItem( $_query, $_type = MYSQL_ASSOC );

    /**
     * update a record from table
     * @param	string		$_table		table name
     * @param	array		$_array 	data array  name => value
     * @param	string		$_conditons		query conditions.
     */
    public function update( $_table, &$_array, $_conditons );

    /**
     * get total records rows number.(获取总记录数)
     * @param		string 		$_table 	table name
     * @param		string		$_conditons		query conditions
     */
    public function count( $_table, $_conditons = NULL );

    /**
     * begin transaction (事物开启)
     */
    public function beginTransaction();

    /**
     * commit transaction (事物提交)
     */
    public function commit();

    /**
     * roll back (事物回滚)
     */
    public function rollBack();

}
?>