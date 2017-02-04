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
use herosphp\utils\ArrayUtils;

Loader::import('model.IModel', IMPORT_FRAME);


class C_Model implements IModel {

    /**
     * 数据库连接资源
     * @var \herosphp\db\mysql\SingleDB
     */
    protected $db;

    //数据表主键
    protected $primaryKey = 'id';
    //是否自动产生ID，如果没有传入的ID的话
    protected $autoPrimary = false;

    /**
     * 配置关联模型，用来对数据表进行垂直分割
     * array(key => array('fields' => '', 'model' => ''))
     * @var array
     */
    protected $flagments = array();
    protected $isFlagment = false;

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
     * 如果开启了数据分段(垂直分割)， 则需要启用事务来实现原子性操作
     */
    public function add($data)
    {
        $data = &$this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        if ( !isset($data[$this->primaryKey]) && $this->autoPrimary ) {
            $data[$this->primaryKey] = StringUtils::genGlobalUid();
        }
        if ( $this->isFlagment ) {
            $this->beginTransaction();
        }
        $result = $this->db->insert($this->table, $data);
        if ( $this->isFlagment ) {

            if ( $result != false ) {
                foreach ( $this->flagments as $value ) {
                    $model = Loader::model($value['model']);
                    $data[$model->getPrimaryKey()] = $data[$this->primaryKey]; //注入关联外键
                    if ( $model->add($data) == false ) {
                        $this->rollback();
                        return false;
                    }
                }
            }

            $this->commit();
        }

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
        if ( !isset($data[$this->primaryKey]) && $this->autoPrimary ) {
            $data[$this->primaryKey] = StringUtils::genGlobalUid();
        }
        if ( $this->isFlagment ) {
            $this->beginTransaction();
        }
        $result = $this->db->replace($this->table, $data);

        if ( $this->isFlagment ) {

            if ( $result != false ) {
                foreach ( $this->flagments as $value ) {
                    $model = Loader::model($value['model']);
                    $data[$model->getPrimaryKey()] = $data[$this->primaryKey]; //注入关联外键
                    if ( $model->replace($data) == false ) {
                        $this->rollback();
                        return false;
                    }
                }
            }

            $this->commit();
        }

        return $result;
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
        if ( $this->isFlagment ) {
            //通过事务来实现原子性操作
            $this->beginTransaction();
            $items = $this->getItems($conditions, $this->primaryKey);
            $ids = array();
            foreach ( $items as $val ) {
                $ids[] = $val[$this->primaryKey];
            }
            unset($items);

            $result = $this->db->delete($this->table, $conditions);
            if ( $result != false ) {
                foreach ( $this->flagments as $value ) {
                    $model = Loader::model($value['model']);
                    $_res = $model->deletes(array(
                        $model->getPrimaryKey() => array('$in' => $ids)
                    ));
                    if ( $_res == false ) {
                        $this->rollback();
                        return false;
                    }
                }
            }

            $this->commit();
            return $result;
        } else {
            return $this->db->delete($this->table, $conditions);
        }
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
        $data = &$this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        if ( $this->isFlagment ) {
            $items = $this->getItems($conditions, $this->primaryKey);
            $ids = array();
            foreach ( $items as $val ) {
                $ids[] = $val[$this->primaryKey];
            }
            unset($items);

            $result = $this->db->update($this->table, $data, $conditions);
            if ( $result != false ) {
                foreach ( $this->flagments as $value ) {
                    $model = Loader::model($value['model']);
                    $model->updates($data, array(
                        $model->getPrimaryKey() => array('$in' => $ids)
                    ));
                }
            }
            return $result;
        } else {
            return $this->db->update($this->table, $data, $conditions);
        }
    }

    /**
     * @see IModel::getItems()
     */
    public function &getItems($conditions, $fields, $order, $limit, $group, $having)
    {
        $items =  $this->db->find($this->table,$conditions, $fields, $order, $limit, $group, $having);

        if ( $items && $this->isFlagment ) {
            //组合id查询条件
            $ids = array();
            foreach ($items as $val) {
                if ( isset($val[$this->primaryKey]) ) {
                    $ids[] = $val[$this->primaryKey];
                }
            }
            //查出关联表的数据，然后合并数据
            if ( !empty($ids) ) {
                foreach( $this->flagments as $value ) {
                    $model = Loader::model($value['model']);
                    $__items = $model->getItems(array(
                        $model->getPrimaryKey() => array('$in' => $ids)
                    ), $value['fields']);
                    //用primaryKey 作为数组的索引
                    $__items = ArrayUtils::changeArrayKey($__items, $model->getPrimaryKey());
                    foreach ( $items as $key => $v ) {
                        if ( !empty($__items[$v[$this->primaryKey]]) ) {
                            $items[$key] = array_merge($v, $__items[$v[$this->primaryKey]]);
                        }
                    }
                    unset($__items);
                }
            }
        }

        //做字段别名映射
        if ( !empty($items) ) {
            $mappings = $this->getMapping();
            if ( !empty($mappings) ) {
                foreach ($items as $key => $value) {
                    foreach ( $mappings as $name => $val ) {
                        $items[$key][$name] = $value[$val];
                        unset($items[$key][$val]); //删除原来映射的数据
                    }
                }
            }
        }
        return $items;
    }

    public function &find()
    {
        return $this->getItems($this->where, $this->fields, $this->sort, $this->limit, $this->group, $this->having);
    }

    /**
     * @see IModel::getItem()
     */
    public function &getItem($condition, $fields, $order)
    {
        if ( !is_array($condition) ) {
            $condition = array($this->primaryKey => $condition);
        }
        $item = $this->db->findOne($this->table, $condition, $fields, $order);

        if ( isset($item[$this->primaryKey]) && $this->isFlagment ) {
            foreach( $this->flagments as $value ) {
                $model = Loader::model($value['model']);
                $__item = $model->getItem($item[$this->primaryKey], $value['fields']);
                if ( !empty($__item) ) {
                    $item = array_merge($item, $__item);
                    unset($__item);
                }
            }
        }
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
       return $this->batchIncrease($field, $offset, array($this->primaryKey => $id));
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

        $query = "UPDATE {$this->table} SET {$update_str} WHERE {$conditions}";
        return ($this->db->excute($query) != false);
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
        return $this->batchReduce($field, $offset, array($this->primaryKey => $id));
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
        $query = "UPDATE {$this->table} SET {$update_str} WHERE {$conditions}";
        return ($this->db->excute($query) != false);
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
     * 写锁定
     * @return boolean
     */
    public function writeLock(){
        return $this->db->excute("LOCK TABLES {$this->table} WRITE");
    }

    /**
     * 读锁定
     * @return boolean
     */
    public function readLock(){
        return $this->db->excute("LOCK TABLES {$this->table} READ");
    }

    /**
     * 解锁
     * @return boolean
     */
    public function unLock(){
        return $this->db->excute("UNLOCK TABLES");
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

    public function limit($page, $size) {
        $this->limit = array($page, $size);
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
