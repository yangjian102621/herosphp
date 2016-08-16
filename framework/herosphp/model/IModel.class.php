<?php
/*---------------------------------------------------------------------
 * Interface model for database access.(数据库访问模型接口)
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/

namespace herosphp\model;

 interface IModel {

     //查询列表
     public function query($sql);

     /**
      * 添加数据
      * @param $data
      * @return int
      */
     public function insert($data);

     /**
      * 替换数据
      * @param $data
      * @return boolean
      */
     public function replace($data);

     /**
      * 删除指定id的数据
      * @param $id
      * @return boolean
      */
     public function delete($id);

     /**
      * 删除指定条件的数据
      * @param $conditions
      * @return boolean
      */
     public function deletes($conditions);

     /**
      * 获取数据列表
      * @param array $conditions
      * @param $fields
      * @param $order
      * @param $limit
      * @param $group
      * @param $having
      * @return mixed
      */
     public function getItems($conditions, $fields, $order, $limit, $group, $having);

     public function find();

     /**
      * 获取单条数据
      * @param $condition
      * @param $fields
      * @param $order
      * @return mixed
      */
     public function getItem($condition, $fields, $order);

     public function findOne();

     /**
      * 更新一条数据
      * @param $data
      * @param $id
      * @return boolean
      */
     public function update($data, $id);

     /**
      * 批量更新数据
      * @param $data
      * @param $conditions
      * @return mixed
      */
     public function updates($data, $conditions);

     /**
      * 获取指定条件的记录总数
      * @param $conditions
      * @return int
      */
     public function count($conditions);

     /**
      * 增加某一字段的值
      * @param tring $field
      * @param int $offset 增量
      * @param int $id
      * @return boolean
      */
     public function increase($field, $offset, $id);

     /**
      * 批量增加指定字段的值
      * @param string $field
      * @param int $offset 增量
      * @param array|string $conditions
      * @return mixed
      */
     public function batchIncrease($field, $offset, $conditions);

     /**
      * 减少某一字段的值
      * @param string $field
      * @param int $offset 增量
      * @param int $id
      * @return mixed
      */
     public function reduce($field, $offset, $id);

     /**
      * 批量减少某一字段的值
      * @param string $field
      * @param int $offset 增量
      * @param array|string $conditions
      * @return mixed
      */
     public function batchReduce($field, $offset, $conditions);

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
     public function sets($field, $value, $conditions);
     
    //开启事务
     public function beginTransaction();
    
    //提交更改
     public function commit();
     
     //回滚
     public function rollback();

     //判断是否开启了事物
     public function inTransaction();

     /**
      * @return IModel
      */
     public function where($where); //设置查询条件

     /**
      * @return IModel
      */
     public function field($fields); //设置查询字段

     /**
      * @return IModel
      */
     public function limit($from, $size);

     /**
      * @return IModel
      */
     public function sort($sort);

     /**
      * @return IModel
      */
     public function group($group);

     /**
      * @return IModel
      */
     public function having($having); //设置分组条件
 }