<?php
/*---------------------------------------------------------------------
 * 数据库操作通用接口，所有数据操作类必须实现这一接口。
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\db\interfaces;

interface Idb {

    /**
     * 执行一条sql语句，直接返回结果
     * @param string $sql
     * @return mixed
     */
    public function excute($sql);


    /**
     * 执行一条sql语句并返回结果列表数组，适用与于自定义查询
     * @param string $query
     * @return mixed
     */
    public function query($query);

    /**
     * 插入数据
     * @param string $table 数据表或者集合名称
     * @param array $data 数据，必须为数组
     * @param bool $return_prikey 是否返回主键的值
     * @return mixed
     */
    public function insert($table, $data, $return_prikey);

    /**
     * 更新数据
     * @param string $table 数据表名称
     * @param array $data 数据
     * @param array $condition 查询条件
     * @param int $return_type 返回类型
     * <p>0=>直接返回true或者false, 1 => 返回本次更新影响的记录条数</p>
     * @return bool|int
     */
    public function update($table, $data, $condition, $return_type=0);

    /**
     * 删除数据
     * @param $table
     * @param array $condition 删除条件
     * @param int $return_type 返回类型
     * <p>0=>直接返回true或者false, 1 => 返回本次更新影响的记录条数</p>
     * @return bool|int
     */
    public function delete($table, $condition, $return_type=0);

    /**
     * 获取数据列表
     * @param $table
     * @param array $condition
     * @param array $field
     * @param array $sort 排序
     * <p>排序规则，array(filed => order_way), order_way的取值有2种， 详细如下：</p>
     * <p>1  : 正序排列，相当于sql中的order by ASC</p>
     * <p>-1 : 倒序排列，相当于sql中的order by DESC</p>
     * @param array $limit 查询limit, 格式:array($skip, $size)
     * @param array $group
     * @param array $having
     * @return array
     */
    public function &find($table,
                          $condition=null,
                          $field=null,
                          $sort=null,
                          $limit=null,
                          $group=null,
                          $having=null);

    /**
     * 获取一条数据
     * @param $table
     * @param array $condition
     * @param array $field
     * @param array $sort
     * @return array|false
     */
    public function &findOne($table, $condition=null, $field=null, $sort=null);

    /**
     * 获取某个条件匹配的总记录数
     * @param $table
     * @param array $condition
     * @return int
     */
    public function count($table, $condition);

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