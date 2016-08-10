<?php

namespace herosphp\db\entity;

/*---------------------------------------------------------------------
 * 数据库增删改查实体类(Entity)，封装所有跟数据库操作有关的参数
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

interface DBEntity {

    /**
     * create a query sql
     * @return
     */
    public function buildQueryString();

    /**
     * build query condition
     * @return
     */
    public function buildWhere();

    /**
     * add AND query conditions
     * @param $field
     * @param $value
     * @return
     */
    public function addWhere($field, $value);

    /**
     * add OR query condition
     * @param $field
     * @param $value
     * @return
     */
    public function addOrWhere($field, $value);

    /**
     * add LIKE keyword query condition
     * @param $field
     * @param $value
     * @return
     */
    public function addLikeWhere($field, $value);

    /**
     * add OR LIKE query condition
     * @param $field
     * @param $value
     * @return
     */
    public function addOrLikeWhere($field, $value);

    /**
     * add special operation condition, such as >= <= != and so on
     * @param $field
     * @param $opt
     * @param $value
     * @return
     */
    public function addOptWhere($field, $opt, $value);

    /**
     * add OR special operation condition, such as >= <= != and so on
     * @param $field
     * @param $opt
     * @param $value
     * @return
     */
    public function addOrOptWhere($field, $opt, $value);

    /**
     * <p>直接设置查询条件字符串, 如果设置了这个条件，其他条件都将失效</p>
     * @param $where
     * @return
     */
    public function where($where);  //设置条件

    public function field($field); //设置字段

    public function pagesize($pagesize); //设置分页大小

    public function page($page); //设置页码

    public function order($order); //设置排序

    public function getTable(); //设置数据表
    public function setTable($table); //获取数据表

    public function setData($data); //注入数据
    public function getData(); //获取数据

}
