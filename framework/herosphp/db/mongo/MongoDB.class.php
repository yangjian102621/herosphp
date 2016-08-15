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
use herosphp\exception\HeroException;
use herosphp\exception\UnSupportedOperationException;

Loader::import('db.interfaces.Idb', IMPORT_FRAME);
class MongoDB implements Idb {

    /**
     * @var \MongoDB
     */
    private $db = null; //数据库对象

    private $configs = array();

    //mongodb操作选项
    private $options = array(
        'fsync' => 0, //是否强制同步写入,mongodb为了保证性能，写入是异步的，先时保存在内存的
        'upsert' => 0, //更新的时候没有符合条件的文档是否创建一条新文档
        'multiple' => 1, //是否更新所有匹配的文档，默认只更新匹配的第一条
        'justOne' => 0, //是否只删除一个
    );

    public function __construct($configs)
    {
        if ( !is_array($configs) || empty($configs) ) E("必须传入数据库的配置信息！");

        $this->configs = $configs;

        $this->connect();

    }

    /**
     * @see Idb::connect
     * @throws HeroException
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
     * @throws UnSupportedOperationException
     */
    public function excute($sql)
    {
        throw new UnSupportedOperationException();
    }

    /**
     * @see Idb::insert
     */
    public function insert($table, $data)
    {
        $collection = $this->db->selectCollection($table);
        $result = $collection->insert($data, $this->getOptions());
        return $result['ok'] == 1;

    }

    /**
     * @see Idb::replace
     * 如果对象来自数据库，则更新现有的数据库对象，否则插入对象。
     */
    public function replace($table, $data)
    {
        $collection = $this->db->selectCollection($table);
        $result = $collection->save($data, $this->getOptions());
        return $result['ok'] == 1;
    }

    /**
     * @see Idb::delete
     */
    public function delete($table, $condition)
    {
        $where = MongoQueryBuilder::where($condition);
        if ( empty($where) || $where == null ) return false;
        $collection = $this->db->selectCollection($table);
        $result = $collection->remove($where, $this->getOptions());
        return ($result['ok'] == 1 && $result['n'] > 0);
    }

    /**
     * @see Idb::find()
     */
    public function &find($table,
                              $condition=null,
                              $field=null,
                              $sort=null,
                              $limit=null,
                              $group=null,
                              $having=null)
    {
        $collection = $this->db->selectCollection($table);
        $where = MongoQueryBuilder::where($condition);
        $limit = MongoQueryBuilder::limit($limit);
        $sort = MongoQueryBuilder::sort($sort);
        $result = $collection->find($where, MongoQueryBuilder::fields($field));
        if ( $limit ) {
            $result->skip($limit[0])->limit($limit[1]);
        }
        if ( !empty($sort) ) {
            $result->sort($sort);
        }
        $items = array();
        if ( $result ) {
            while ( $result->hasNext() ) {
                $items[] = $result->next();
            }
        }
        return $items;
    }

    /**
     * @see Idb::findOne()
     */
    public function &findOne($table, $condition=null, $field=null, $sort=null)
    {
        $collection = $this->db->selectCollection($table);
        return $collection->findOne(MongoQueryBuilder::where($condition), MongoQueryBuilder::fields($field));
    }

    /**
     * @see Idb::update
     */
    public function update($table, $data, $condition)
    {
        $where = MongoQueryBuilder::where($condition);
        if ( empty($where) || $where == null ) return false;

        $collection = $this->db->selectCollection($table);
        $result = $collection->update(
            $where,
            array('$set' => $data),
            $this->getOptions());

        return ($result['ok'] == 1 && $result['n'] > 0);
    }

    //增加或者减少某个字段的值(必须是整数)
    public function inc($table, $data, $condition)
    {
        $collection = $this->db->selectCollection($table);
        $result = $collection->update(
            MongoQueryBuilder::where($condition),
            array('$inc' => $data),
            $this->getOptions());

        return ($result['ok'] == 1 && $result['n'] > 0);
    }

    /**
     * @see Idb::count
     */
    public function count($table, $conditions)
    {
        $collection = $this->db->selectCollection($table);
        return $collection->count(MongoQueryBuilder::where($conditions));
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

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

}
