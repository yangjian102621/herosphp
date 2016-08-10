<?php
/*---------------------------------------------------------------------
 * 数据库访问模型model mongodb实现
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
use herosphp\db\entity\DBEntity;
use herosphp\db\entity\MongoEntity;
use herosphp\filter\Filter;

Loader::import('model.IModel', IMPORT_FRAME);


class MongoModel implements IModel {

    /**
     * 数据库连接资源
     * @var \herosphp\db\mongo\MongoDB
     */
    private $db;

    /**
     * 数据表名称
     * @var string
     */
    private $table = '';

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
            $congfig = Loader::config('db');
        }
        //创建数据库
        $this->db = DBFactory::createDB('mongo', $congfig['mongo']);
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
        $entity = MongoEntity::getInstance()
            ->setTable($this->table)
            ->setData($data);
        return $this->db->insert($entity);
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
        $entity = MongoEntity::getInstance()
            ->setTable($this->table)
            ->setData($data);
        return $this->db->replace($entity);
    }

    /**
     * @see IModel::delete()
     */
    public function delete($id)
    {
        return $this->deletes($id);
    }

    /**
     * @see IModel::deletes()
     */
    public function deletes($conditions)
    {
        return $this->db->delete($this->getConditons($conditions));
    }

    /**
     * @see IModel::getItems()
     */
    public function getItems(DBEntity $entity)
    {
        if ( $entity == null ) {
            $entity = MongoEntity::getInstance();
        }
        $entity->setTable($this->table);
        return  $this->db->getList($entity);

    }

    /**
     * @see IModel::getItem()
     */
    public function getItem($conditions)
    {
        return $this->db->getOneRow($this->getConditons($conditions));
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
        if ( $data == false ) {
            return false;
        }
        $entity = MongoEntity::getInstance()
            ->setTable($this->table)
            ->setData($data)
            ->addWhere('_id', new \MongoId($id));
        return $this->db->update($entity);
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
        $entity = MysqlEntity::getInstance()
            ->setTable($this->table)
            ->setData($data)
            ->where($conditions);
        return $this->db->update($entity);
    }

    /**
     * @see IModel::count()
     * @param $conditions
     * @return int
     */
    public function count($conditions)
    {
        $entity = MysqlEntity::getInstance()
            ->setTable($this->table)
            ->where($conditions);
        return $this->db->count($entity);
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
        $data = array('$inc' => array($field => $offset));
        return $this->update($data, $id);
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
        $data = array('$inc' => array($field => $offset));
        return $this->updates($data, $conditions);
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
        return $this->increase($field, - $offset, $id);
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
        return $this->batchIncrease($field, - $offset, $conditions);
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
        return $this->updates($data, $conditions);
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
     * 获取查询条件
     * @param $conditons
     * @return
     */
    private function getConditons($conditions) {
        if ( !$conditions instanceof DBEntity ) {
            $__conditions = MongoEntity::getInstance()->setTable($this->table);
            if ( is_array($conditions) ) {
                $__conditions->where($conditions);
            } else {
                $__conditions->addWhere('_id', new \MongoId($conditions));
            }
            return $__conditions;
        }
        return $conditions;
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
     * 获取数据连接对象
     * @return \herosphp\db\interfaces\Idb
     */
    public function getDB() {
        return $this->db;
    }
}
