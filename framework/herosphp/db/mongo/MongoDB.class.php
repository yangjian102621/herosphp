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
use herosphp\exception\UnSupportedOperationException;

Loader::import('db.interfaces.Idb', IMPORT_FRAME);
class MongoDB implements Idb {

    private $conn = null; //数据库连接对象

    private $configs = array();

    public function __construct($configs)
    {
        if ( !is_array($configs) || empty($configs) ) E("必须传入数据库的配置信息！");

        $this->configs = $configs;

    }

    /**
     * 连接数据库
     * @return mixed
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

        $conn->selectDB($this->configs['db']);
        $this->conn = $conn;
    }

    /**
     * @param string $query
     * @throws UnSupportedOperationException
     */
    public function query($query)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * 插入数据
     * @param string $table 数据表
     * @param array $data 数据载体
     * @return int 最后插入数据id
     */
    public function insert($table, &$data)
    {
        $collection = $this->conn->selectCollection($table);
        $data['save_at'] = new \MongoDate();
        return $collection->insert($data);
    }

    /**
     * 插入一条数据，如果数据存在就更新它
     * @param string $table 数据表
     * @param array $data 数据载体
     * @return boolean
     */
    public function replace($table, &$data)
    {
        // TODO: Implement replace() method.
    }

    /**
     * @param string $table 删除数据
     * @param string $condition 查询条件
     * @return boolean
     */
    public function delete($table, $condition = null)
    {
        // TODO: Implement delete() method.
    }

    /**
     * 获取数据列表
     * @param string $query
     * @return array
     */
    public function &getList($query)
    {
        // TODO: Implement getList() method.
    }

    /**
     * 获取一条数据
     * @param sting $query
     * @return array
     */
    public function &getOneRow($query)
    {
        // TODO: Implement getOneRow() method.
    }

    /**
     * 更新数据
     * @param string $table 数据表名
     * @param array $data 数据载体
     * @param string $condition 查询条件
     * @return boolean
     */
    public function update($table, &$data, $condition = null)
    {
        // TODO: Implement update() method.
    }

    /**
     * 获取总记录数
     * @param string $table
     * @param string $conditions
     * @return int
     */
    public function count($table, $conditions = null)
    {
        // TODO: Implement count() method.
    }

    /**
     * begin transaction (事物开启)
     */
    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * commit transaction (事物提交)
     */
    public function commit()
    {
        // TODO: Implement commit() method.
    }

    /**
     * roll back (事物回滚)
     */
    public function rollBack()
    {
        // TODO: Implement rollBack() method.
    }

    /**
     * 检查是否开启了事物
     * @return boolean
     */
    public function inTransaction()
    {
        // TODO: Implement inTransaction() method.
    }
}
