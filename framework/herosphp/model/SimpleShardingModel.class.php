<?php
/*---------------------------------------------------------------------
 * 简单分片模型 <br />
 * 注意 : 此模型为简单分片模型，不能作为主数据表的模型，只能作为扩展表的模型，比如文章内容，简介，描述等字段可以进行分片。<br />
 * 该模型不支持一些批量操作方法，比如 updates(), deletes() 方法和 find() 方法，所以该模型不支持单独查询列表。<br />
 * 使用该模型，则ID不能使用数据库自增ID，需要使用程序生成分布式唯一ID
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/

namespace herosphp\model;

use herosphp\core\Loader;
use herosphp\core\WebApplication;
use herosphp\db\DBFactory;
use herosphp\db\mysql\MysqlQueryBuilder;
use herosphp\exception\UnSupportedOperationException;
use herosphp\filter\Filter;
use herosphp\string\StringUtils;
use herosphp\utils\HashUtils;

Loader::import('model.IModel', IMPORT_FRAME);


class SimpleShardingModel implements IModel {

    /**
     * 数据库连接资源
     * @var \herosphp\db\mysql\SingleDB
     */
    protected $db;

    //数据表主键
    protected $primaryKey = 'id';

    //分片数量,推荐是用质数(3,5,7,11,13...)
    protected $shardingNum = 7;

    //数据表名称
    protected $table = '';

    /**
     * 字段映射
     * @var array
     * array(别名 => 字段名)
     */
    protected $mapping = array();

    /**
     * 数据过滤规则
     * @var array
     */
    protected $filterMap = array();

    /**
     * 初始化数据库连接
     * @param string $table 数据表
     * @param array $config 数据库配置信息
     */
    public function __construct( $table, $config = null ) {

        //初始化数据库配置
        if ( !$config ) {
            //默认使用第一个数据库服务器配置
            $dbConfigs = Loader::config('db');
            $db_config = $dbConfigs['mysql'];
            $this->table = $table;
            if ( DB_ACCESS == DB_ACCESS_SINGLE ) {  //单台服务器
                $config = $db_config[0];
            } else if ( DB_ACCESS == DB_ACCESS_CLUSTERS ) { //多台服务器
                $config = $db_config;
            }

        }
        //创建数据库连接对象
        $this->db = DBFactory::createDB(DB_ACCESS, $config);
    }

    /**
     * @param $sql
     * @return mixed|\PDOStatement
     */
    public function query($sql)
    {
        return $this->db->query($sql);
    }

    /**
     * @see IModel::add()
     */
    public function add($data)
    {
        $data = &$this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        //自动生成ID
        $data[$this->primaryKey] = StringUtils::genGlobalUid();
        //根据ID获取分片
        $table = $this->getShardingTables($data[$this->primaryKey]);
        $result = $this->db->insert($table, $data);
        if ( $result === true ) { //非自增ID
            $result = $data[$this->primaryKey];
        }
        return $result;
    }

    /**
     * @see IModel::replace()
     */
    public function replace($data)
    {
        $data = &$this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        //自动生成ID
        $data[$this->primaryKey] = StringUtils::genGlobalUid();
        //根据ID获取分片
        $table = $this->getShardingTables($data[$this->primaryKey]);
        return $this->db->replace($table, $data);
    }

    /**
     * @see IModel::delete()
     */
    public function delete($id)
    {
        $where = array($this->primaryKey => $id);
        $table = $this->getShardingTables($id);
        return $this->db->delete($table, $where);;
    }

    /**
     * @see IModel::deletes()
     */
    public function deletes($conditions)
    {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    /**
     * @see IModel::update()
     * @param $data
     * @param $id
     * @return bool
     */
    public function update($data, $id)
    {
        $where = array($this->primaryKey => $id);
        $table = $this->getShardingTables($id);
        return $this->db->update($table, $data, $where);
    }

    /**
     * @see IModel::updates()
     * @param $data
     * @param $conditions
     * @return bool|void
     * @throws UnSupportedOperationException
     */
    public function updates($data, $conditions)
    {
        throw new UnSupportedOperationException('暂时不支持该操作。');

    }

    /**
     * @notice : 该方法支持传入ID数组来查询
     * @param $conditions
     * @return array|mixed
     */
    public function &getItems($ids, $fields, $order, $limit, $group, $having)
    {
        $items = array();
        foreach ( $ids as $id ) {
            $items[] = $this->getItem($id);
        }
        return $items;
    }

    public function &find()
    {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    /**
     * @notice 该方法只支持传入单个ID作为查询条件
     * @param $id
     * @return array|bool|false|mixed
     */
    public function &getItem($id, $fields, $order)
    {
        if ( is_array($id) ) {
            E('该方法只支持传入单个ID作为查询条件');
        }
        $condition = array($this->primaryKey => $id);
        $table = $this->getShardingTables($id);
        $item = $this->db->findOne($table, $condition);

        //做字段别名映射
        $mappings = $this->getMapping();
        if ( !empty($mappings) ) {
            foreach ( $mappings as $name => $val ) {
                $item[$name] = $item[$val];
                unset($item[$val]);
            }
        }
        return $item;
    }

    public function &findOne()
    {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    /**
     * @see IModel::count()
     * @param $conditions
     * @return int
     */
    public function count($conditions)
    {
        $tables = $this->getShardingTables();
        if ( is_string($tables) ) {
            return $this->db->count($tables, $conditions);
        }

        $total = 0;
        foreach ($tables as $table) {
            $total += $this->db->count($table, $conditions);
        }
        return $total;
    }

    /**
     * @see IModel::increase()
     * @param tring $field
     * @param int $offset
     * @param int $id
     * @return bool|\PDOStatement
     */
    public function increase($field, $offset, $id)
    {
        $update_str = '';
        if ( is_array($field) && is_array($offset) && count($field) == count($offset) ) {
            foreach ( $field as $key => $value ) {
                $updateUnit = "{$value}=CONCAT({$value}, '{$offset[$key]}')";
                if ( is_numeric($offset[$key]) ) {
                    $updateUnit = "{$value}={$value} + {$offset[$key]}";
                }
                $update_str .= $update_str == '' ? $updateUnit : ','.$updateUnit;
            }
        } else {
            if ( is_numeric($offset) ) {
                $update_str .= "{$field}={$field} + {$offset}";
            } else {
                $update_str .= "{$field}=CONCAT({$field}, '{$offset}')";
            }
        }
        $table = $this->getShardingTables($id);
        $conditions = MysqlQueryBuilder::buildConditions(array($this->primaryKey => $id));
        $query = "UPDATE {$table} SET {$update_str} WHERE {$conditions}";
       return $this->db->execute($query);
    }

    /**
     * @see IModel::batchIncrease()
     * @param string $field
     * @param int $offset
     * @param array|string $conditions
     * @return mixed|\PDOStatement
     * @throws UnSupportedOperationException
     */
    public function batchIncrease($field, $offset, $conditions)
    {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    /**
     * @see IModel::reduce()
     * @param string $field
     * @param int $offset
     * @param int $id
     * @return mixed|\PDOStatement
     */
    public function reduce($field, $offset, $id)
    {
        $update_str = '';
        if ( is_array($field) && is_array($offset) && count($field) == count($offset) ) {
            foreach ( $field as $key => $value ) {
                $updateUnit = "{$value}=REPLACE({$value}, '{$offset[$key]}', '')";
                if ( is_numeric($offset[$key]) ) {
                    $updateUnit = "{$value}={$value} - {$offset[$key]}";
                }
                $update_str .= $update_str == '' ? $updateUnit : ','.$updateUnit;
            }
        } else {
            if ( is_numeric($offset) ) {
                $update_str .= "{$field}={$field} - {$offset}";
            } else {
                $update_str .= "{$field}=REPLACE({$field}, '{$offset}', '')";
            }
        }
        $table = $this->getShardingTables($id);
        $conditions = MysqlQueryBuilder::buildConditions(array($this->primaryKey => $id));
        $query = "UPDATE {$table} SET {$update_str} WHERE {$conditions}";
        return $this->db->execute($query) == false;
    }

    /**
     * @see IModel::batchReduce()
     * @param string $field
     * @param int $offset
     * @param array|string $conditions
     * @return mixed|\PDOStatement
     * @throws UnSupportedOperationException
     */
    public function batchReduce($field, $offset, $conditions)
    {
        throw new UnSupportedOperationException('暂时不支持该操作。');

    }

    /**
     * @see IModel::set()
     * @param $field
     * @param $value
     * @param $id
     * @return bool|mixed
     */
    public function set($field, $value, $id)
    {
        $data = array($field => $value);
        return $this->update($data, $id);
    }

    /**
     * @see IModel::sets()
     * @param $field
     * @param $value
     * @param $conditions
     * @return bool|mixed
     * @throws UnSupportedOperationException
     */
    public function sets($field, $value, $conditions)
    {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    /**
     * @see IModel::beginTransaction()
     */
    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }

    /**
     * @see IModel::commit()
     */
    public function commit()
    {
        $this->db->commit();
    }

    /**
     * @see IModel::rollback()
     */
    public function rollback()
    {
        $this->db->rollBack();
    }

    /**
     * @see IModel::inTransaction()
     */
    public function inTransaction()
    {
        return $this->db->inTransaction();
    }

    /**
     * 获取过滤后的数据
     * @param $data
     * @return mixed
     */
    protected function &loadFilterData(&$data) {

        if ( empty($this->filterMap) ) {
            return $data;
        }
        $error = null;
        $_data = Filter::loadFromModel($data, $this->filterMap, $error);

        if ( $_data == false ) {
            WebApplication::getInstance()->getAppError()->setCode(1);
            WebApplication::getInstance()->getAppError()->setMessage($error);
        }
        return $_data;
    }

    /**
     * @param array $mapping
     */
    public function setMapping($mapping)
    {
        $this->mapping = $mapping;
    }

    /**
     * @return array
     */
    public function getMapping()
    {
        return $this->mapping;
    }

    /**
     * @param string $primaryKey
     */
    public function setPrimaryKey($primaryKey)
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * 设置表名
     * @param $table
     */
    public function setTable($table) {
        $this->table = $table;
    }

    /**
     * 根据路由信息获取分片表
     * @param $router
     * @return string|void
     */
    public function getShardingTables($router) {

        if ( is_numeric($router) ) {
            $shardingNode = intval($router) % $this->shardingNum;
            return $this->table.'_'.$shardingNode;
        }

        if ( is_string($router) ) {
            $router = HashUtils::DJPHash($router);
            return $this->table.'_'.($router % $this->shardingNum);
        }

        return $this->__getAllShardingTables();
    }

    //获取所有的数据分片表
    public function __getAllShardingTables() {

        $tables = array();
        for ($i = 0; $i < $this->shardingNum; $i++ ) {
            $tables[] = $this->table.'_'.$i;
        }
        return $tables;
    }

    /**
     * 获取数据连接对象
     * @return \herosphp\db\interfaces\Idb
     */
    public function getDB() {
        return $this->db;
    }

    public function where($where) {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    public function field($fields) {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    public function limit($page, $size) {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    public function sort($sort) {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    public function group($group) {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    public function having($having) {
        throw new UnSupportedOperationException('暂时不支持该操作。');
    }

    /**
     * 写锁定
     * @return boolean
     */
    public function writeLock()
    {
        //将所有的表锁定
        $tables = $this->getShardingTables();
        foreach ($tables as $value) {
            $this->db->execute("LOCK TABLES {$value} WRITE");
        }
        return true;
    }

    /**
     * 读锁定
     * @return boolean
     */
    public function readLock()
    {
        //将所有的表锁定
        $tables = $this->getShardingTables();
        foreach ($tables as $value) {
            $this->db->execute("LOCK TABLES {$value} READ");
        }
        return true;
    }

    /**
     * 解锁
     * @return boolean
     */
    public function unLock()
    {
        return $this->db->execute("UNLOCK TABLES");
    }
}
