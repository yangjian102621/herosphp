<?php
/*---------------------------------------------------------------------
 * 数据库访问模型的MySQL实现
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/

namespace herosphp\model;

 class C_Model implements IModel {
     
     /**
      * instance to mysqli
      * 
      * @var    DMysqli     
      */
     private $_db = NULL;
     
     /**
      * database table for model (当前要操作的表)
      * 
      * @var        string
      */
     private $_table = NULL;
     
     /**
      * primary key of table
      * @var        string (数据表的主键)
      */
     private  $_primary_key = 'id';
     
     //constructor
     public function __construct( $_table, $_db_config = NULL ) {
     	
         $this->_table = SysCfg::$db_table[$_table];
         //默认使用第一数据库
         if ( $_db_config == NULL ) $_db_config = SysCfg::$db_info[0];
         //获取数据库的访问模式
         $_access = SysCfg::$db_access;
         $this->_db = DBFactory::createDB($_access, $_db_config);
		 
     }
     
     /**
      * set primary key of table(设置主键) 
      */
     public function setPrimaryKey( $_key ) {
         $this->_primary_key = $_key;
         
     }
     
     /**
      * @see        IModel::query(); 
      */
     public function query( $_sql ) {
         return $this->_db->query($_sql);
     }
        
     /**
      * @see        IModel::insert()
      */
     public function insert( $_data ) {
     	
		 //净化数据
		 if ( !get_magic_quotes_gpc() ) {
			if ( is_array($_data) ) {
				foreach ( $_data as $_name => $_v ) {
					$_data[$_name] = mysql_real_escape_string($_v);
				}
			}
		 }
		 
         return $this->_db->insert($this->_table, $_data);
     }
     
      /**
      * @see        IModel::replace()
      */
     public function replace( $_data ) {
         return $this->_db->replace($this->_table, $_data);
     }
     
      /**
      * @see        IModel::delete()
      */
     public function delete( $_id ) {
         return $this->_db->delete($this->_table, "id={$_id}");
     }
     
      /**
      * @see        IModel::deletes()
      */
     public function deletes( $_conditions ) {
     	
         $_conditions = SQL::create()->where($_conditions)->buildConditions();
         return $this->_db->delete($this->_table, $_conditions);
		 
     }
     
      /**
      * @see        IModel::getList()
      */
     public function getList( $_conditions, $_fields, $_order, $_limit, $_group, $_having ) {
     	
         $_sql = SQL::create()->fields($_fields)->table($this->_table)->where($_conditions)->order($_order)
                ->group($_group)->having($_having)->limit($_limit)->getSQL();

         return  $this->_db->getList($_sql);
		 
     }
     
     //创建 SQL   
     public function createSQL( $_conditions, $_fields, $_order, $_limit, $_group, $_having ) {
     	
         $_sql = SQL::create()->fields($_fields)->table($this->_table)->where($_conditions)->order($_order)
                ->group($_group)->having($_having)->limit($_limit)->getSQL();
         return  $_sql;
     }
     
     //通过SQL获取多条记录
     public function getItems($_sql) {
         return  $this->_db->getList($_sql);
     }
     
      /**
      * @see        IModel::getOneRow()
      */
     public function getOneRow( $_conditions, $_fields, $_order, $_group, $_having ) {
     	
         $_sql = SQL::create()->fields($_fields)->table($this->_table)->where($_conditions)->order($_order)
                ->group($_group)->having($_having)->getSQL();
				
         return $this->_db->getOneRow($_sql);
     
	 }
     
     //通过SQL获取单条记录
     public function getOneItem( $_sql ) {
         return $this->_db->getOneRow($_sql);
     }
      /**
      * @see        IModel::update()
      */
     public function update( $_data, $_id ) {
         return $this->_db->update($this->_table, $_data, "{$this->_primary_key}='{$_id}'");
     }
     
     /**
      * @see        IModel::updates()
      */
     public function updates( $_data, $_conditions ) {
         $_conditions = SQL::create()->where($_conditions)->buildConditions();
         return $this->_db->update($this->_table, $_data, $_conditions);
     }
     
     /**
      * @see        IModel::count(); 
      */
     public function count( $_condi ) {
         return $this->_db->count($this->_table, $_condi);
     }
     
      /**
      * @see        IModel::affectedRows(); 
      */
     public function affectedRows( $_condi = NULL, $_fields="id" ) {
         return $this->_db->affectedRows();
     }
     
     /**
      * @see        IModel::begin()
      */
     public function begin() {
         $this->_db->begin();
     }
    
     /**
      * @see        IModel::commit()
      */
     public function commit() {
         $this->_db->commit();
     }
     
      /**
      * @see        IModel::rollback()
      */
     public function rollback() {
         $this->_db->rollBack();
     }
     
     /**
      * get database size 
      */
     public function getDataSize() {
         return DMysqli::getDataSize();
     }
     
     /**
      * get database version  
      */
     public function getDBVersion() {
         return DMysqli::dbVersion();
     }
	 
	 public function getDB() {
	 	return $this->_db;
	 }
 }
?>