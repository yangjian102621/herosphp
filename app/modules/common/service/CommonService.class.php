<?php

namespace common\service;

use common\service\interfaces\ICommonService;
use herosphp\core\Loader;

Loader::import('common.service.interfaces.ICommonService', IMPORT_APP);
/**
 * 通用服务接口实现
 * Class CommonService
 * @package common\service
 */
abstract class CommonService implements ICommonService {

    /**
     * 数据模型操作DAO
     * @var \common\dao\interfaces\ICommonDao
     */
    protected $modelDao;

    /**
     * @param \common\dao\interfaces\ICommonDao $modelDao
     */
    public function setModelDao($modelDao)
    {
        $this->modelDao = $modelDao;
    }

    /**
     * @return \common\dao\interfaces\ICommonDao
     */
    public function getModelDao()
    {
        return $this->modelDao;
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function add($data)
    {
        return $this->modelDao->add($data);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function replace($data)
    {
        return $this->modelDao->replace($data);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function delete($id)
    {
        return $this->modelDao->delete($id);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function deletes($conditions)
    {
        return $this->modelDao->deletes($conditions);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function getItems($conditions, $fields, $order, $page, $pagesize, $group, $having)
    {
        return $this->modelDao->getItems($conditions, $fields, $order, $page, $pagesize, $group, $having);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function getItem($conditions, $fields, $order, $group, $having)
    {
        return $this->modelDao->getItem($conditions, $fields, $order, $group, $having);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function update($data, $id)
    {
        return $this->modelDao->update($data, $id);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function updates($data, $conditions)
    {
        return $this->modelDao->updates($data, $conditions);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function count($conditions)
    {
        return $this->modelDao->count($conditions);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function increase($field, $offset, $id)
    {
        return $this->modelDao->increase($field, $offset, $id);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function batchIncrease($field, $offset, $conditions)
    {
        return $this->modelDao->batchIncrease($field, $offset, $conditions);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function reduce($field, $offset, $id)
    {
        return $this->modelDao->reduce($field, $offset, $id);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function batchReduce($field, $offset, $conditions)
    {
        return $this->modelDao->batchReduce($field, $offset, $conditions);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function set($field, $value, $id)
    {
        return $this->modelDao->set($field, $value, $id);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function sets($field, $value, $conditions)
    {
        return $this->modelDao->sets($field, $value, $conditions);
    }

    /**
     * @see \common\service\interfaces\ICommonService::add()
     */
    public function beginTransaction()
    {
        $this->modelDao->beginTransaction();
    }

    /**
     * @see \common\service\interfaces\ICommonService::commit()
     */
    public function commit()
    {
        $this->modelDao->commit();
    }

    /**
     * @see \common\service\interfaces\ICommonService::rollback()
     */
    public function rollback()
    {
        $this->modelDao->rollback();
    }

    /**
     * @see \common\service\interfaces\ICommonService::inTransaction()
     */
    public function inTransaction()
    {
        return $this->modelDao->inTransaction();
    }

    /**
     * @see \common\service\interfaces\ICommonService::getDB()
     */
    public function getDB()
    {
        return $this->modelDao->getDB();
    }


}

?>