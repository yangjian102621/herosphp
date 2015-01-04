<?php
/*---------------------------------------------------------------------
 * Interface model for database access.(数据库访问模型接口)
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/

namespace herosphp\model;

 interface IModel {

     /**
      * 执行一条sql语句
      * @param $sql
      * @return mixed
      */
     public function query( $sql );

     /**
      * 添加数据
      * @param $data
      * @return int
      */
     public function insert( $data );

     /**
      * 替换数据
      * @param $data
      * @return boolean
      */
     public function replace( $data );

     /**
      * 删除指定id的数据
      * @param $id
      * @return boolean
      */
     public function delete( $id );

     /**
      * 删除指定条件的数据
      * @param $conditions
      * @return boolean
      */
     public function deletes( $conditions );

     /**
      * 获取数据列表
      * @param $conditions 查询条件
      * @param $fields 查询字段
      * @param $order 排序
      * @param $page 当前页
      * @param $pagesize 每页数量
      * @param $group 分组字段
      * @param $having 分组条件
      * @return array
      */
     public function getItems( $conditions, $fields, $order, $page, $pagesize, $group, $having );

     /**
      * 获取单条数据
      * @param $conditions 查询条件
      * @param $fields 查询字段
      * @param $order 排序
      * @param $group 分组字段
      * @param $having 分组条件
      * @return mixed
      */
     public function getItem( $conditions, $fields, $order, $group, $having );

     /**
      * 更新一条数据
      * @param $data
      * @param $id
      * @return boolean
      */
     public function update( $data, $id );

     /**
      * 批量更新数据
      * @param $data
      * @param $conditions
      * @return mixed
      */
     public function updates( $data, $conditions );

     /**
      * 获取指定条件的记录总数
      * @param $conditions
      * @return int
      */
     public function count( $conditions );

     /**
      * 增加某一字段的值
      * @param $field
      * @param $offset 增量
      * @param $id
      * @return boolean
      */
     public function increase( $field, $offset, $id );

     /**
      * 批量增加指定字段的值
      * @param $field
      * @param $offset
      * @param $conditions
      * @return mixed
      */
     public function batchIncrease( $field, $offset, $conditions );

     /**
      * 减少某一字段的值
      * @param $field
      * @param $offset
      * @param $id
      * @return mixed
      */
     public function reduce( $field, $offset, $id );

     /**
      * 批量减少某一字段的值
      * @param $field
      * @param $offset
      * @param $conditions
      * @return mixed
      */
     public function batchReduce( $field, $offset, $conditions );

     /**
      * 更新某一字段的值(快捷方法，一次只能更新一个字段)
      * @param $field
      * @param $value
      * @param $id
      * @return mixed
      */
     public function set($field, $value, $id);

     /**
      * 批量更新某一字段的值
      * @see set()
      * @param $field
      * @param $value
      * @param $conditions
      * @return mixed
      */
     public function sets( $field, $value, $conditions );
     
    //开启事务
     public function beginTransaction();
    
    //提交更改
     public function commit();
     
     //回滚
     public function rollback();
 }
?>