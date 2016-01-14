<?php

namespace common\dao;

use common\dao\interfaces\ICommonDao;
use herosphp\core\Loader;

Loader::import('common.dao.interfaces.ICommonDao', IMPORT_APP);

/**
 * 通用记录访问对象(DAO)接口的通用实现
 * Class CommonDao
 * @package common\dao
 * @author yangjian102621@163.com
 */
abstract class CommonDao implements ICommonDao {

    /**
     * 数据库操作模型
     * @var \herosphp\model\C_Model
     */
    protected $modelDao;

    /**
     * 构造函数，初始化modelDao
     * @param $model
     */
    public function __construct( $model ) {

        $this->setModelDao(Loader::model($model));
    }

    /**
     * @param \herosphp\model\C_Model $modelDao
     */
    public function setModelDao($modelDao)
    {
        $this->modelDao = $modelDao;
    }

    /**
     * @return \herosphp\model\C_Model
     */
    public function getModelDao()
    {
        return $this->modelDao;
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::add
     */
    public function add($data)
    {
        return $this->modelDao->insert($data);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::replace
     */
    public function replace($data)
    {
        return $this->modelDao->replace($data);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::delete
     */
    public function delete($id)
    {
        return $this->modelDao->delete($id);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::deletes
     */
    public function deletes($conditions)
    {
        return $this->modelDao->deletes($conditions);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::getItems
     */
    public function getItems($conditions, $fields, $order, $page, $pagesize, $group, $having)
    {
        return $this->modelDao->getItems($conditions, $fields, $order, $page, $pagesize, $group, $having);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::getItem
     */
    public function getItem($conditions, $fields, $order, $group, $having)
    {
        return $this->modelDao->getItem($conditions, $fields, $order, $group, $having);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::update
     */
    public function update($data, $id)
    {
        return $this->modelDao->update($data, $id);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::updates
     */
    public function updates($data, $conditions)
    {
        return $this->modelDao->updates($data, $conditions);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::count
     */
    public function count($conditions)
    {
        return $this->modelDao->count($conditions);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::increase
     */
    public function increase($field, $offset, $id)
    {
        return $this->modelDao->increase($field, $offset, $id);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::batchIncrease
     */
    public function batchIncrease($field, $offset, $conditions)
    {
        return $this->modelDao->batchIncrease($field, $offset, $conditions);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::reduce
     */
    public function reduce($field, $offset, $id)
    {
        return $this->modelDao->reduce($field, $offset, $id);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::batchReduce
     */
    public function batchReduce($field, $offset, $conditions)
    {
        return $this->modelDao->batchReduce($field, $offset, $conditions);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::set
     */
    public function set($field, $value, $id)
    {
        return $this->modelDao->set($field, $value, $id);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::sets
     */
    public function sets($field, $value, $conditions)
    {
        return $this->modelDao->sets($field, $value, $conditions);
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::beginTransaction
     */
    public function beginTransaction()
    {
        $this->modelDao->beginTransaction();
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::commit
     */
    public function commit()
    {
        $this->modelDao->commit();
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::rollback
     */
    public function rollback()
    {
        $this->modelDao->rollback();
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::inTransaction
     */
    public function inTransaction()
    {
        return $this->modelDao->inTransaction();
    }

    /**
     * @see \common\dao\interfaces\ICommonDao::getDB
     */
    public function getDB() {
        return $this->getModelDao()->getDB();
    }
}

?>