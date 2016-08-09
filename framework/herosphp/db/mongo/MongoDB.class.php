<?php
/*---------------------------------------------------------------------
 * 单数居库服务器操作的PDO实现
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\db\mongo;

use herosphp\core\Loader;
use herosphp\db\interfaces\Idb;
use herosphp\db\interfaces\sting;
use herosphp\db\entity\DBEntity;
use herosphp\exception\UnSupportedOperationException;

Loader::import('db.interfaces.Idb', IMPORT_FRAME);
class MongoDB implements Idb {

    private $db = null; //数据库连接对象

    private $configs = array();

    public function __construct($configs)
    {
        if ( !is_array($configs) || empty($configs) ) E("必须传入数据库的配置信息！");

        $this->configs = $configs;

    }

    /**
     * @see Idb::connect
     * @throws \herosphp\exception\HeroException
     */
    public function connect()
    {
        try {
            if ( $this->configs['user'] ) {
                $conn = new \MongoClient("mongodb://{$this->configs['user']}:{$this->configs['pass']}@{$this->configs['host']}:{$this->configs['port']}");
            } else {
                $conn = new \MongoClient("mongodb://{$this->configs['host']}:{$this->configs['port']}");
            }
        } catch (\MongoConnectionException $e) {
            E("Failed to connect to database ".$e->getMessage());
        }

        $this->db = $conn->selectDB($this->configs['db']);
    }

    /**
     * @throws UnSupportedOperationException
     */
    public function query($query)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * @see Idb::insert
     */
    public function insert(DBEntity $entity)
    {
        $collection = $this->db->selectCollection($entity->getTable());
        $options = array('fsync' => true); //同步插入
        $result = $collection->insert($entity->getData(), $options);
        return $result['ok'] == 1;
    }

    /**
     * @see Idb::replace
     * @throws UnSupportedOperationException
     */
    public function replace(DBEntity $entity)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * @see Idb::delete
     */
    public function delete(DBEntity $entity)
    {
        // TODO: Implement delete() method.
    }

    /**
     * 获取数据列表
     * @param string $query
     * @return array
     */
    public function &getList(DBEntity $entity)
    {
        // TODO: Implement getList() method.
    }

    /**
     * 获取一条数据
     * @param DBEntity $query
     * @return array
     */
    public function &getOneRow(DBEntity $entity)
    {
        $collection = $this->db->selectCollection($entity->getTable());
        return $collection->findOne($entity->buildWhere());
    }

    /**
     * @see Idb::update
     */
    public function update(DBEntity $entity)
    {
        $collection = $this->db->selectCollection($entity->getTable());
        $options = array('fsync' => true); //同步插入
        $result = $collection->update($entity->buildWhere(), $entity->getData(), $options);
        return $result['ok'] == 1;
    }

    /**
     * @see Idb::count
     */
    public function count(DBEntity $entity)
    {
        // TODO: Implement count() method.
    }

    /**
     * begin transaction (事物开启)
     */
    public function beginTransaction()
    {
        throw new UnSupportedOperationException();
    }

    /**
     * commit transaction (事物提交)
     */
    public function commit()
    {
        throw new UnSupportedOperationException();
    }

    /**
     * roll back (事物回滚)
     */
    public function rollBack()
    {
        throw new UnSupportedOperationException();
    }

    /**
     * 检查是否开启了事物
     * @return bool
     * @throws UnSupportedOperationException
     */
    public function inTransaction()
    {
        throw new UnSupportedOperationException();
    }
}
