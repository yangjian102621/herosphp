<?php
/**
 * Postgresql数据库操作类， 实现Idb.class.php 接口。
 * Postgresql database operation class, implements class Idb.
 * ---------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * ----------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version 1.1 
 * @completed	2013-04-10
 */
class Postgresql implements Idb {
	/**
	 * insert a record to database.
	 * @param		string		$_table		table name
	 * @param		array		$_fields	data array  field => value
	 * @return		int 		insert_id	return the last insert id 
	 */
	public function insert( $_table, &$_array ) {}
	
	/**
	 * delete a record from table.
	 * @param		string		$_table  	table name
	 * @param		string		$_where		query condition.
	 * @return 		mixed		false for faild, retrun affacts rows if success.
	 */
	public function delete( $_table, $_where = NULL ) {}
	
	/**
	 * execute an SQL
	 * @param		string  	$_sql		SQL
	 */
	public function query( $_sql ) {}
	
	/**
	 * Get a list of data records.
	 * @param		string		$_sql
	 * @param		int 		$_type		type of array to the result(返回结果集的类型)
	 * @param    	int 		$_serial    cache serial(缓存分类标志)
	 */
	public function &getList( $sql, $_serial = -1, $_type = NULL ) {}
	
	/**
	 * get one data records
	 * @param		string		$_sql
	 * @param		array		$_result	records of query.
	 * @param    	int 		$_serial    cache serial(缓存分类标志)
	 */
	public function &getOneRow( $_sql, $_serial = -1, $_type = NULL ) {}
	
	/**
	 * update a record from table
	 * @param	string		$_table		table name
	 * @param	array		$_array 	data array  name => value
	 * @param	string		$_where		query conditions. 
	 */
	public function update( $_table, &$_array, $_where ) {}
	
	/**
	 * get row number of the specified query
	 * @param		string		$_sql
	 */
	public function getRowsNum( $sql ) {}
	
	/**
	 * get total records rows number.
	 * @param		string 		$_table 	table name
	 * @param		string		$_fields	fields to query
	 * @param		string		$_where		query conditions
	 */
	public function getTotalRecords( $_table, $_fields="Id", $_where = NULL ) {}
}
?>