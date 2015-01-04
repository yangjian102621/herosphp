<?php
/*---------------------------------------------------------------------
 * 数据库访问模型model dao实现
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 * @version 1.2.1
 *-----------------------------------------------------------------------*/

namespace herosphp\model;

use herosphp\core\Loader;
use herosphp\db\DBFactory;
use herosphp\db\SQL;

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
     * 字段映射
     * @var array
     */
    private $mapping = array();

    /**
     * 数据过滤规则
     * @var array
     */
    private $rules = array();

    /**
     * 初始化数据库连接
     * @param string $table 数据表
     * @param array $config 数据库配置信息
     */
    public function __construct( $table, $config = null ) {

        //加载数据表配置
        $tableConfig = Loader::config('table', 'db');
        $this->table = $tableConfig[$table];

        //初始化数据库配置
        if ( !$config ) {
            //默认使用一个数据库服务器配置
            $dbConfigs = Loader::config('hosts', 'db');
            $db_config = $dbConfigs[DB_TYPE];
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
     * @see IModel::query()
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
        // TODO: Implement insert() method.
    }

    /**
     * @see IModel::replace()
     */
    public function replace($data)
    {
        // TODO: Implement replace() method.
    }

    /**
     * @see IModel::delete()
     */
    public function delete($id)
    {
        // TODO: Implement delete() method.
    }

    /**
     * @see IModel::deletes()
     */
    public function deletes($conditions)
    {
        // TODO: Implement deletes() method.
    }

    /**
     * @see IModel::getItems()
     */
    public function getItems($conditions, $fields, $order, $page, $pagesize, $group, $having)
    {
        $limit = null;
        if ( $pagesize > 0 && $page > 0 ) $limit = array(($page-1) * $pagesize, $pagesize);
        $sql = SQL::create($this->primaryKey)->table($this->table)->where($conditions)->fields($fields)
            ->order($order)->group($group)->having($having)->limit($limit)->buildQueryString();
        return $this->db->getItems($sql);
    }

    /**
     * @see IModel::getItem()
     */
    public function getItem($conditions, $fields, $order, $group, $having)
    {
        $sql = SQL::create($this->primaryKey)->table($this->table)->where($conditions)->fields($fields)
            ->order($order)->group($group)->having($having)->buildQueryString();
        return $this->db->getItem($sql);
    }

    /**
     * @see IModel::update()
     */
    public function update($data, $id)
    {
        // TODO: Implement update() method.
    }

    /**
     * @see IModel::updates()
     */
    public function updates($data, $conditions)
    {
        // TODO: Implement updates() method.
    }

    /**
     * @see IModel::count()
     */
    public function count($conditions)
    {
        // TODO: Implement count() method.
    }

    /**
     * @see IModel::increase()
     */
    public function increase($field, $offset, $id)
    {
        // TODO: Implement increase() method.
    }

    /**
     * @see IModel::batchIncrease()
     */
    public function batchIncrease($field, $offset, $conditions)
    {
        // TODO: Implement batchIncrease() method.
    }

    /**
     * @see IModel::reduce()
     */
    public function reduce($field, $offset, $id)
    {
        // TODO: Implement reduce() method.
    }

    /**
     * @see IModel::batchReduce()
     */
    public function batchReduce($field, $offset, $conditions)
    {
        // TODO: Implement batchReduce() method.
    }

    /**
     * @see IModel::set()
     */
    public function set($field, $value, $id)
    {
        // TODO: Implement set() method.
    }

    /**
     * @see IModel::sets()
     */
    public function sets($field, $value, $conditions)
    {
        // TODO: Implement sets() method.
    }

    /**
     * @see IModel::beginTransaction()
     */
    public function beginTransaction()
    {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * @see IModel::commit()
     */
    public function commit()
    {
        // TODO: Implement commit() method.
    }

    /**
     * @see IModel::rollback()
     */
    public function rollback()
    {
        // TODO: Implement rollback() method.
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
     * @param array $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @param string $table
     */
    public function setTable($table)
    {
        $this->table = $table;
    }

    /**
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

}
?>