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
     * add left brackets(添加左边括号)
     * @param $prefix  bracket's prefix default is 'AND'(括号的前缀，默认式 AND)
     * @return
     */
    public function addLeftBrackets($prefix='AND');

    /**
     * add right brackets(添加右边括号)
     * @return
     */
    public function addRightBrackets();

    /**
     * 添加 IN 查询条件
     * @param $where
     * @return
     */
    public function addInWhere($field, $value);

    public function addOrInWhere($field, $value);

    /**
     * <p>直接设置查询条件字符串, 如果设置了这个条件，其他条件都将失效</p>
     * <p>set where condition direct, if set the condition, other condition will does not work</p>
     * @param $where
     * @return
     */
    public function setWhereString($where);

    public function setPagesize($pagesize);

    public function setPage($page);

    public function setOrder($order);

    public function setGroup($group);

    public function setHaving($having);

    public function setTable($table);

}
