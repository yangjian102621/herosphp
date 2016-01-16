<?php

namespace common\dao\interfaces;

/**
 * 通用记录访问对象(DAO)接口
 * Interface ICommonDao
 * @package common\dao\interfaces
 * @author yangjian102621@gmail.com
 */
interface ICommonDao {

    /**
     * 添加数据
     * @param array $data
     * @return int
     */
    public function add( $data );

    /**
     * 替换数据
     * @param array $data
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
     * @param array|string $conditions
     * @return boolean
     */
    public function deletes( $conditions );

    /**
     * 获取数据列表
     * @param string|array $conditions 查询条件
     * @param string|array $fields 查询字段
     * @param string|array $order 排序
     * @param int $page 当前页
     * @param int $pagesize 每页数量
     * @param string $group 分组字段
     * @param string|array $having 分组条件
     * @return array
     */
    public function getItems( $conditions, $fields, $order, $page, $pagesize, $group, $having );

    /**
     * 获取单条数据
     * @param string|array $conditions 查询条件
     * @param string|array $fields 查询字段
     * @param string|array $order 排序
     * @param string $group 分组字段
     * @param string|array $having 分组条件
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
     * @param string $field
     * @param int $offset 增量
     * @param int $id
     * @return boolean
     */
    public function increase( $field, $offset, $id );

    /**
     * 批量增加指定字段的值
     * @param string $field
     * @param int $offset 增量
     * @param array|string $conditions
     * @return mixed
     */
    public function batchIncrease( $field, $offset, $conditions );

    /**
     * 减少某一字段的值
     * @param string $field
     * @param int $offset 增量
     * @param int $id
     * @return mixed
     */
    public function reduce( $field, $offset, $id );

    /**
     * 批量减少某一字段的值
     * @param string $field
     * @param int $offset 增量
     * @param array|string $conditions
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

    //判断是否开启了事物
    public function inTransaction();

    /**
     * 获取数据库对象
     * @return \herosphp\db\interfaces\Idb
     */
    public function getDB();
}