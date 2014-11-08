<?php
/**
 * 数据库操作统一接口，所有数据操作类必须实现这一接口。
 * database operate class common interface.
 * ---------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * ----------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @version 1.1
 * @completed	2013-04-08
 */
interface Idb {
	
	/**
	 * insert a record to database.
	 * @param		string		$_table		table name
	 * @param		array		$_fields	data array  field => value
	 * @return		int 		insert_id	return the last insert id 
	 */
	public function insert( $_table, &$_array );
	
	/**
	 * delete a record from table.
	 * @param		string		$_table  	table name
	 * @param		string		$_where		query condition.
	 * @return 		mixed		false for faild, retrun affacts rows if success.
	 */
	public function delete( $_table, $_where = NULL );
	
	/**
	 * execute an SQL
	 * @param		string  	$_sql		SQL
	 */
	public function query( $_sql );
	
	/**
	 * Get a list of data records.
	 * @param		string		$_sql
	 * @param		int 		$_type		type of array to the result(返回结果集的类型)
	 * @return 		array		$_result	records of query.
	 */
	public function &getList( $sql, $_type = NULL );
	
	/**
	 * get one data records
	 * @param		string		$_sql
	 * @return 		array		$_result	records of query.
	 */
	public function &getOneRow( $_sql, $_type = NULL );
	
	/**
	 * update a record from table
	 * @param	string		$_table		table name
	 * @param	array		$_array 	data array  name => value
	 * @param	string		$_where		query conditions. 
	 */
	public function update( $_table, &$_array, $_where );
	
	/**
	 * get total records rows number.(获取总记录数)
	 * @param		string 		$_table 	table name
	 * @param		string		$_where		query conditions
	 */
	public function count( $_table, $_where );
		 
}
?>