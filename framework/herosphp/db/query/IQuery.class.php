<?php

namespace herosphp\db\query;

/*---------------------------------------------------------------------
 * 数据库查询对象接口
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

interface IQuery {

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
    public function where($where);

    public function field($field);

    public function pagesize($pagesize);

    public function page($page);

    public function order($order);

    public function group($group);

    public function having($having);

    public function setTable($table);

}
