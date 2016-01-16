<?php
/*---------------------------------------------------------------------
 * 数据库访问模型model dao实现
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
use herosphp\db\DBFactory;
use herosphp\db\SQL;
use herosphp\filter\Filter;
use herosphp\utils\AjaxResult;

Loader::import('model.IModel', IMPORT_FRAME);


class C_Model implements IModel {

    /**
     * 数据库连接资源
     * @var \herosphp\db\interfaces\Idb
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
    private $table;

    /**
     * 数据表映射，适合多表水平分割
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
            $db_config = $dbConfigs[DB_TYPE];
            if ( DB_ACCESS == DB_ACCESS_SINGLE ) {  //单台服务器
                $config = $db_config[0];
            } else if ( DB_ACCESS == DB_ACCESS_CLUSTERS ) { //多台服务器
                $config = $db_config;
            }

        }
        $this->table = $config['table_prefix'].$table;
        //创建数据库
        $this->db = DBFactory::createDB(DB_ACCESS, $config);
    }

    /**
     * @see IModel::query()
     * @param $sql
     * @return mixed|\PDOStatement
     */
    public function query($sql)
    {
        return $this->db->query($sql);
    }

    /**
     * @see IModel::insert()
     * @param $data
     * @return int
     */
    public function insert($data)
    {
        $data = $this->loadFilterData($data);
        return $this->db->insert($this->table, $data);
    }

    /**
     * @see IModel::replace()
     * @param $data
     * @return bool
     */
    public function replace($data)
    {
        $data = $this->loadFilterData($data);
        return $this->db->replace($this->table, $data);
    }

    /**
     * @see IModel::delete()
     * @param $id
     * @return bool
     */
    public function delete($id)
    {
        return $this->db->delete($this->table, "{$this->primaryKey}={$id}");
    }

    /**
     * @see IModel::deletes()
     * @param $conditions
     * @return bool
     */
    public function deletes($conditions)
    {
        $conditions = SQL::create()->buildConditions($conditions);
        return $this->db->delete($this->table, $conditions);
    }

    /**
     * @see IModel::getItems()
     * @param array|string $conditions
     * @param array|string $fields
     * @param array|string $order
     * @param int $page
     * @param int $pagesize
     * @param string $group
     * @param array|string $having
     * @return array
     */
    public function getItems($conditions, $fields, $order, $page, $pagesize, $group, $having)
    {
        $limit = null;
        if ( $pagesize > 0 && $page > 0 ) $limit = array(($page-1) * $pagesize, $pagesize);
        $sql = SQL::create($this->primaryKey)->table($this->table)->where($conditions)->fields($fields)
            ->order($order)->group($group)->having($having)->limit($limit)->buildQueryString();
        $items =  $this->db->getItems($sql);

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

    /**
     * @see IModel::getItem()
     * @param array|string $conditions
     * @param array|string $fields
     * @param array|string $order
     * @param string $group
     * @param array|string $having
     * @return array|mixed
     */
    public function getItem($conditions, $fields, $order, $group, $having)
    {
        $sql = SQL::create($this->primaryKey)->table($this->table)->where($conditions)->fields($fields)
            ->order($order)->group($group)->having($having)->buildQueryString();
        $item = $this->db->getItem($sql);

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

    /**
     * @see IModel::update()
     * @param $data
     * @param $id
     * @return bool
     */
    public function update($data, $id)
    {
        $data = $this->loadFilterData($data);
        return $this->db->update($this->table, $data, "{$this->primaryKey}={$id}");
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
        $conditions = SQL::create()->buildConditions($conditions);
        return $this->db->update($this->table, $data, $conditions);
    }

    /**
     * @see IModel::count()
     * @param $conditions
     * @return int
     */
    public function count($conditions)
    {
        $conditions = SQL::create()->buildConditions($conditions);
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
        $conditions = SQL::create($this->primaryKey)->buildConditions($id);
        $query = "UPDATE {$this->table} SET {$field}={$field}+{$offset} WHERE {$conditions}";
        return $this->db->query($query);
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
        $conditions = SQL::create($this->primaryKey)->buildConditions($conditions);
        $query = "UPDATE {$this->table} SET {$field}={$field}+{$offset} WHERE {$conditions}";
        return $this->db->query($query);
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
        $conditions = SQL::create($this->primaryKey)->buildConditions($id);
        $query = "UPDATE {$this->table} SET {$field}={$field}-{$offset} WHERE {$conditions}";
        return $this->db->query($query);
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
        $conditions = SQL::create($this->primaryKey)->buildConditions($conditions);
        $query = "UPDATE {$this->table} SET {$field}={$field}-{$offset} WHERE {$conditions}";
        return $this->db->query($query);
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
        return $this->db->update($this->table, $data, "{$this->primaryKey}={$id}");
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
        $conditions = SQL::create()->buildConditions($conditions);
        return $this->db->update($this->table, $data, $conditions);
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

            //如果开启了事物操作，则先回滚
            if ( $this->inTransaction() ) {
                $this->rollback();
            }
            AjaxResult::ajaxResult('error', $error);
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
}
?>
