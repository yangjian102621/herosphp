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

use herosphp\db\entity\DBEntity;

interface Idb {

    /**
     * 连接数据库
     * @return mixed
     */
    public function connect();

    /**
     * 插入数据
     * @param DBEntity $entity 数据库实体对象
     * @return mixed
     */
    public function insert(DBEntity $entity);

    /**
     * 插入一条数据，如果数据存在就更新它
     * @param DBEntity $entity
     * @return bool
     */
    public function replace(DBEntity $entity);

    /**
     * 删除数据
     * @param DBEntity $entity
     * @return mixed
     */
    public function delete(DBEntity $entity);

    /**
     * 获取数据列表
     * @param DBEntity $entity
     * @return mixed
     */
    public function &getList(DBEntity $entity);

    /**
     * 获取一条数据
     * @param DBEntity $entity
     * @return mixed
     */
    public function &getOneRow(DBEntity $entity);

    /**
     * 更新数据
     * @param DBEntity $entity
     * @return mixed
     */
    public function update(DBEntity $entity);

    /**
     * 获取总记录数
     * @param DBEntity $entity
     * @return mixed
     */
    public function count(DBEntity $entity);

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