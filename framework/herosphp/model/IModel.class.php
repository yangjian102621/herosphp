<?php
/**
 * Interface model for database access.(数据库访问模型接口) 
 * 
 * @author      yangjian<yangjian102621@gmail.com>
 * @since       2013.10.08
 */
 interface IModel {
     
     /**
      * send a SQL to database, and get the excuted result
      * 
      * @param      string          $_sql 
      */
     public function query( $_sql );
     
     /**
      * insert a record to database
      * 
      * @param      array           $_data         data array.
      * @return     int             return last insert id            
      */
     public function insert( $_data );
     
     /**
      * insert or update a record to database
      * 
      * @param      array           $_data         data array.
      * @return     boolean           
      */
     public function replace( $_data );
     
     /**
      * delete a records from database.
      * 
      * @param      int         $_id  
      * @return     boolean      
      */
     public function delete( $_id );
     
     /**
      * delete records base conditions
      * 
      * @param      string | array      $_conditions 
      * @return      boolean
      */
     public function deletes( $_conditions );
     
     /**
      * get data collections
      * 
      * @param      string | array          $_fields
      * @param      string | array          $_conditions 
      * @param      string | array          $_order
      * @param      string | array          $_group
      * @param      string | array          $_having
      * @param      int | string | array    $_limit
      * @return     array
      */
     public function getList( $_conditions, $_fields, $_order, $_limit, $_group, $_having );
     
      /**
      * get one record
      * 
      * @param      string | array          $_fields
      * @param      string | array          $_conditions 
      * @param      string | array          $_order
      * @param      string | array          $_group
      * @param      string | array          $_having
      * @return     array
      */
     public function getOneRow( $_conditions, $_fields, $_order, $_group, $_having );
    
      /**
      * delete the specified record.
      * 
      * @param      array          $_data
      * @param      int            $_id
      * @return     boolean
      */
     public function update( $_data, $_id );
     
     /**
      * delete records as conditions
      * 
      * @param      array                     $_data
      * @param      array | string            $_conditions
      * @return     boolean
      */
     public function updates( $_data, $_conditions );
     
     /**
      * get the total number of records
      * 
      * @param      string      $_condi (conditions for query)
      */
     public function count( $_condi );
     
     /**
      * get the affected rows
      */
     public function affectedRows();
     
    //begin transaction 
     public function begin();
    
    //commit
     public function commit();
     
     //rollback
     public function rollback();
 }
?>