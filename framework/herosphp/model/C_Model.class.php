<?php
/*---------------------------------------------------------------------
 * 数据库访问模型model mysql实现
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
use herosphp\filter\Filter;
use herosphp\string\StringUtils;

Loader::import('model.IModel', IMPORT_FRAME);


class C_Model implements IModel {

    /**
     * 数据库连接资源
     * @var \herosphp\db\mysql\SingleDB
     */
    private $db;

    /**
     * 数据表主键
     * @var string
     */
    private $primaryKey = 'id';

    /**
     * 数据表名称
     * @var string
     */
    private $table = '';

    private $tablePrefix = '';

    /**
     * 数据表映射，适合多表水平分割,如
     * array('user_1', 'user_2', 'user_3')
     * @var array
     */
    private $tableMapping = array();

    /**
     * 字段映射
     * @var array
     * 别名 => 字段名
     * addTime => add_time
     */
    private $mapping = array();

    /**
     * 数据过滤规则
     * @var array
     */
    private $filterMap = array();

    private $where = array();

    private $fields = array();

    private $sort = array();

    private $limit = array();

    private $group = '';

    private $having = array();

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
            $this->tablePrefix = $db_config[0]['table_prefix'];
            $this->table = $this->tablePrefix.$table;
            if ( DB_ACCESS == DB_ACCESS_SINGLE ) {  //单台服务器
                $config = $db_config[0];
            } else if ( DB_ACCESS == DB_ACCESS_CLUSTERS ) { //多台服务器
                $config = $db_config;
            }

        }
        //创建数据库
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
     * @see IModel::insert()
     */
    public function insert($data)
    {
        $data = $this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        if ( !isset($data[$this->primaryKey]) ) {
            $data[$this->primaryKey] = StringUtils::genGlobalUid();
        }
        $id = $this->db->insert($this->table, $data);
        if ( $id === true ) {
            $id = $data[$this->primaryKey];
        }
        return $id;
    }

    /**
     * @see IModel::replace()
     */
    public function replace($data)
    {
        $data = $this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        return $this->db->replace($this->table, $data);
    }

    /**
     * @see IModel::delete()
     */
    public function delete($id)
    {
        $where = array($this->primaryKey => $id);
        return $this->deletes($where);
    }

    /**
     * @see IModel::deletes()
     */
    public function deletes($conditions)
    {
        return $this->db->delete($this->table, $conditions);
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
        return $this->updates($data, $where);
    }

    /**
     * @see IModel::updates()
     * @param $data
     * @param $conditions
     * @return bool|mixed
     */
    public function updates($data, $conditions)
    {
        $data = $this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        return $this->db->update($this->table, $data, $conditions);
    }

    /**
     * @see IModel::getItems()
     */
    public function getItems($conditions, $fields, $order, $limit, $group, $having)
    {
        $items =  $this->db->find($this->table,$conditions, $fields, $order, $limit, $group, $having);

        //做字段别名映射
        if ( !empty($items) ) {
            $mappings = $this->getMapping();
            if ( !empty($mappings) ) {
                foreach ($items as $key => $value) {
                    foreach ( $mappings as $name => $val ) {
                        $items[$key][$name] = $value[$val];
                        unset($items[$key][$val]);
                    }
                }
            }
        }
        return $items;
    }

    public function find()
    {
        return $this->getItems($this->where, $this->fields, $this->sort, $this->limit, $this->group, $this->having);
    }

    /**
     * @see IModel::getItem()
     */
    public function getItem($condition, $fields, $order)
    {
        if ( !is_array($condition) ) {
            $condition = array($this->primaryKey => $condition);
        }
        $item = $this->db->findOne($this->table, $condition, $fields, $order);

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

    public function findOne()
    {
        return $this->getItem($this->where, $this->fields, $this->sort);
    }

    /**
     * @see IModel::count()
     * @param $conditions
     * @return int
     */
    public function count($conditions)
    {
        return $this->db->count($this->table, $conditions);
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
        $conditions = MysqlQueryBuilder::buildConditions(array($this->primaryKey => $id));
        $query = "UPDATE {$this->table} SET {$field}={$field}+{$offset} WHERE {$conditions}";
        return $this->db->excute($query);
    }

    /**
     * @see IModel::batchIncrease()
     * @param string $field
     * @param int $offset
     * @param array|string $conditions
     * @return mixed|\PDOStatement
     */
    public function batchIncrease($field, $offset, $conditions)
    {
        $conditions = MysqlQueryBuilder::buildConditions($conditions);
        $query = "UPDATE {$this->table} SET {$field}={$field}+{$offset} WHERE {$conditions}";
        return $this->db->excute($query);
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
        $conditions = MysqlQueryBuilder::buildConditions(array($this->primaryKey => $id));
        $query = "UPDATE {$this->table} SET {$field}={$field}-{$offset} WHERE {$conditions}";
        return $this->db->excute($query);
    }

    /**
     * @see IModel::batchReduce()
     * @param string $field
     * @param int $offset
     * @param array|string $conditions
     * @return mixed|\PDOStatement
     */
    public function batchReduce($field, $offset, $conditions)
    {
        $conditions = MysqlQueryBuilder::buildConditions($conditions);
        $query = "UPDATE {$this->table} SET {$field}={$field}-{$offset} WHERE {$conditions}";
        return $this->db->excute($query);
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
     */
    public function sets($field, $value, $conditions)
    {
        $data = array($field => $value);
        $this->updates($data, $conditions);
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
    protected function loadFilterData(&$data) {

        $filterMap = $this->getFilterMap();
        if ( empty($filterMap) ) {
            return $data;
        }
        $error = null;
        $_data = Filter::loadFromModel($data, $filterMap, $error);

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
     * @param array $filter
     */
    public function setFilterMap($filter)
    {
        $this->filterMap = $filter;
    }

    /**
     * @return array
     */
    public function getFilterMap()
    {
        return $this->filterMap;
    }

    /**
     * 设置表名
     * @param $table
     */
    public function setTable($table) {
        $this->table = $table;
    }

    /**
     * @return array
     */
    public function getTableMapping()
    {
        return $this->tableMapping;
    }

    /**
     * @param array $tableMapping
     */
    public function setTableMapping($tableMapping)
    {
        $this->tableMapping = $tableMapping;
    }

    /**
     * 获取数据连接对象
     * @return \herosphp\db\interfaces\Idb
     */
    public function getDB() {
        return $this->db;
    }

    public function where($where) {
        $this->where = $where;
        return $this;
    }

    public function field($fields) {
        $this->fields = $fields;
        return $this;
    }

    public function limit($from, $size) {
        $this->limit = array($from, $size);
        return $this;
    }

    public function sort($sort) {
        $this->sort = $sort;
        return $this;
    }

    public function group($group) {
        $this->group = $group;
        return $this;
    }

    public function having($having) {
        $this->having = $having;
        return $this;
    }
}
