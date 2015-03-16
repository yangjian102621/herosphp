<?php
/*---------------------------------------------------------------------
 * 数据库操作通用接口，所有数据操作类必须实现这一接口。
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\db\interfaces;

interface Idb {

    /**
     * 连接数据库
     * @return mixed
     */
    public function connect();

    /**
     * 执行一条SQL语句
     * @param string $query 查询语句
     * @return \PDOStatement
     */
    public function query( $query );

    /**
     * 插入数据
     * @param string $table 数据表
     * @param array $data 数据载体
     * @return int 最后插入数据id
     */
    public function insert( $table, &$data );

    /**
     * 插入一条数据，如果数据存在就更新它
     * @param string $table 数据表
     * @param array $data 数据载体
     * @return boolean
     */
    public function replace($table, &$data );

    /**
     * @param string $table 删除数据
     * @param string $condition 查询条件
     * @return boolean
     */
    public function delete( $table, $condition = null );

    /**
     * 获取数据列表
     * @param string $query
     * @param int $resultType 返回结果类（默认为关联数组）
     * @return array
     */
    public function &getItems( $query, $resultType = MYSQL_ASSOC );

    /**
     * 获取一条数据
     * @param sting $query
     * @param int $resultType 返回结果类（默认为关联数组）
     * @return array
     */
    public function &getItem( $query, $resultType = MYSQL_ASSOC );

    /**
     * 更新数据
     * @param string $table 数据表名
     * @param array $data 数据载体
     * @param string $condition 查询条件
     * @return boolean
     */
    public function update( $table, &$data, $condition = null );

    /**
     * 获取总记录数
     * @param string $table
     * @param string $conditions
     * @return int
     */
    public function count( $table, $conditions = null );

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

    /**
     * 检查是否开启了事物
     * @return boolean
     */
    public function inTransaction();

}
?>