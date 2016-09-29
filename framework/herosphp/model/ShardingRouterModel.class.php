<?php
/*---------------------------------------------------------------------
 * 分片数据模型实现，适用于主表数据的关联数据，比如文章评论，用户图片等。 <br />
 * 注意:一般都需要有分片路由的（如userid, aid等）如果没有分片路由，则在查询数据列表(getItems()方法)的时候，会出现数据误差 < br />
 * 具体表现为，如果使用了分页查询，假设你的分片数量为shardingNum = 7，则当分页的page > shardingNum 的时候会有部分数据查不到 </ br>
 * 分片的数据映射越均匀，则这个误差越小。
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
use herosphp\utils\HashUtils;

Loader::import('model.IModel', IMPORT_FRAME);


class ShardingRouterModel implements IModel {

    /**
     * 数据库连接资源
     * @var \herosphp\db\mysql\SingleDB
     */
    protected $db;

    //数据表主键
    protected $primaryKey = 'id';
    //是否自动产生ID，如果没有传入的ID的话
    protected $autoPrimary = true;

    //分片数量,推荐是用质数(3,5,7,11,13...)
    protected $shardingNum = 7;

    //数据表名称
    protected $table = '';

    //分片路由,一般为关联外键(userid, aid)
    protected $shardingRouter = null;

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
     */
    public function add($data)
    {
        $data = &$this->loadFilterData($data);
        if ( $data == false ) {
            return false;
        }
        if ( $this->autoPrimary ) {
            $data[$this->primaryKey] = StringUtils::genGlobalUid();
        }

        if ( $this->shardingRouter == null || is_array($this->shardingRouter) ) {
            E('The sharding router is invalid.');
        }
        $table = $this->getShardingTables($this->shardingRouter);
        $result = $this->db->insert($table, $data);
        if ( $result === true ) {
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
        if ( $this->autoPrimary ) {
            $data[$this->primaryKey] = StringUtils::genGlobalUid();
        }
        if ( $this->shardingRouter == null || is_array($this->shardingRouter) ) {
            E('The sharding router is invalid.');
        }
        $table = $this->getShardingTables($this->shardingRouter);
        return $this->db->replace($table, $data);
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
        $tables = $this->getShardingTables($this->shardingRouter);
        if ( is_string($tables) ) {
            return $this->db->delete($tables, $conditions);
        }
        if ( is_array($tables) && !empty($tables) ) {
            //至少要删除了 一条信息，则表示删除成功了
            $result = false;
            foreach ( $tables as $table ) {
                $result += $this->db->delete($table, $conditions);
            }

            return $result >= 1 ? $result : false;
        }
        return false;
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

        $tables = $this->getShardingTables($this->shardingRouter);
        if ( is_string($tables) ) {
            return $this->db->update($tables, $data, $conditions);
        }

        if ( is_array($tables) && !empty($tables) ) {
            //通过事务来实现原子性操作
            $this->beginTransaction();
            foreach ( $tables as $table ) {
                if ( $this->db->update($table, $data, $conditions) == false ) {
                    $this->rollback();
                    return false;
                }
            }
            $this->commit();
            return true;
        }

        return false;

    }

    /**
     * @see IModel::getItems()
     */
    public function getItems($conditions, $fields, $order, $limit, $group, $having)
    {
        $tables = $this->getShardingTables($this->shardingRouter);
        if ( is_string($tables) ) {

            $items =  $this->db->find($tables, $conditions, $fields, $order, $limit, $group, $having);

        } else if ( is_array($tables) && !empty($tables) ) {    //多表查询，合并排序

            /**
             * 如果是用php进行自定义排序的话，查询字段一定要包含排序字段
             * 如果没有包含，则自动追加进去
             */
            if ( is_string($fields) && $fields != '*' ) {
                $fields = explode(',', $fields);
            }
            $validateOrder = self::getValidateOrder($order);
            if ( $validateOrder != false && $fields != '*' ) {
                foreach ( $validateOrder['sort_field'] as $value ) {
                    $fields[] = $value;
                }
            }

            /**
             * 1. 分别到各个分片去查询，得到结果合并
             * 2. 合并后的查询结果重新排序
             * 3. 根据limit获取数组的前面N个元素
             */
            $results = array();
            $size   = 0;
            $start  = 0;
            if ( $limit ) {
                $limitArr = MysqlQueryBuilder::parseLimitAsArray($limit);;
                $offset = $limitArr[0];
                $size   = $limitArr[1];
                $shardingLen = count($tables);

                /**
                 * 计算分页核心算法， 为了保证分页的时候不遗漏数据，每个分片每次查询的数据是 $size * shardingLen
                 * 重新计算offset 和 pagesize
                 */
                $start  = $offset % ($shardingLen * $size);
                $pageno = max(1, ceil(($offset/$size + 1) / $shardingLen));
                $offset = ($pageno - 1) * $size;
                $bsize  = $size * $shardingLen;
                $_limit = "{$offset},{$bsize}";
            }

            foreach ( $tables as $table ) {
                $__items = $this->db->find($table, $conditions, $fields, $order, $_limit, $group, $having);
                if ( !empty($__items) ) {
                    //合并查询结果
                    $results = array_merge($results, $__items);
                    unset($__items);
                }
            }

            //对查询结果重新排序
            if ( $validateOrder != false ) {
                 self::sortResults($validateOrder, $results);

                //取排序后的前 pagesize 条记录
                if ( $limit && $size < count($results) ) {
                    $items = array_slice($results, $start, $size);
                    unset($results);
                } else {
                    $items = &$results;
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
        $tables = $this->getShardingTables($this->shardingRouter);
        if ( is_string($tables) ) {
            $item = $this->db->findOne($tables, $condition, $fields, $order);

        } elseif ( is_array($tables) ) {

            /**
             * 如果是用php进行自定义排序的话，查询字段一定要包含排序字段
             * 如果没有包含，则自动追加进去
             */
            if ( is_string($fields) && $fields != '*' ) {
                $fields = explode(',', $fields);
            }
            $validateOrder = self::getValidateOrder($order);
            if ( $validateOrder != false && $fields != '*' ) {
                foreach ( $validateOrder['sort_field'] as $value ) {
                    $fields[] = $value;
                }
            }

            //获取每个分片的查询结果，然后再再根据排序，取出第一条
            $results = array();
            foreach ( $tables as $table ) {
                $__item = $this->db->findOne($table, $condition, $fields, $order);
                if ( $__item == false ) continue;
                $results[] = $__item;
            }

            //对查询结果重新排序
            if ( $validateOrder != false ) {
                self::sortResults($validateOrder, $results);
            }
            $item = $results[0];

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

    /**
     * 对查询结果进行重新排序
     * @param $validateOrder 排序方式数组
     * array('sort_field' => $sortField, 'sort_way' => $sortWay)
     * @param $results 查询结果，引用传值
     */
    protected static function sortResults($validateOrder, &$results) {

        if ( count($results) == 1 ) return;
        $sortParams = array(); //排序参数
        foreach ( $validateOrder['sort_field'] as $key => $sortField ) {
            //build the dimension data array
            $dimension = array();
            foreach ( $results as $value ) {
                $dimension[] = $value[$sortField];
            }
            $sortParams[] = $dimension;
            //build the sort way
            if ( $validateOrder['sort_way'][$key] == 'DESC' ) {
                $sortParams[] = SORT_DESC;
            } else {
                $sortParams[] = SORT_ASC;
            }
        }

        $sortParams[] = &$results;
        call_user_func_array('array_multisort', $sortParams);
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
        $tables = $this->getShardingTables($this->shardingRouter);
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
        $update_str = "{$field}=CONCAT({$field}, '{$offset}')";
        if ( is_numeric($offset) ) {
            $update_str = "{$field}={$field}+{$offset}";
        }
        $tables = $this->getShardingTables($this->shardingRouter);
        if ( is_string($tables) ) {
            $query = "UPDATE {$tables} SET {$update_str} WHERE {$conditions}";
            return ($this->db->excute($query) != false);
        }

        if ( is_array($tables) && !empty($tables) ) {
            //通过事务来实现原子性操作
            $this->beginTransaction();
            foreach ( $tables as $table ) {
                $query = "UPDATE {$table} SET {$update_str} WHERE {$conditions}";
                if ( $this->db->excute($query) == false ) {
                    $this->rollback();
                    return false;
                }
            }
            $this->commit();
            return true;
        }
        return false;
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
        $update_str = "{$field}=REPLACE({$field}, '{$offset}', '')";
        if ( is_numeric($offset) ) {
            $update_str = "{$field}={$field}-{$offset}";
        }
        $tables = $this->getShardingTables($this->shardingRouter);
        if ( is_string($tables) ) {
            $query = "UPDATE {$tables} SET {$update_str} WHERE {$conditions}";
            return $this->db->excute($query);
        }

        if ( is_array($tables) ) {
            $this->beginTransaction();
            foreach ($tables as $table) {
                $query = "UPDATE {$table} SET {$update_str} WHERE {$conditions}";
                if ( $this->db->excute($query) == false ) {
                    $this->rollback();
                    return false;
                }
            }
            $this->commit();
            return true;
        }

        return false;

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

        if ( is_array($router) ) { //来自多个分片的路由
            $tables = array();
            foreach ( $router as $value ) {
                if ( is_string($value) ) {
                    $value = HashUtils::DJPHash($value);
                }
                $shardingNode = $value % $this->shardingNum;
                $tables[$shardingNode] = $this->table.'_'.$shardingNode;
            }
            return $tables;
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

    //设置分片路由
    public function setShardingRouter($shardingRouter)
    {
        $this->shardingRouter = $shardingRouter;
    }

    //获取有效的排序
    public static function getValidateOrder($order) {

        if ( !$order ) return false;
        $sortField = array();   //排序字段
        $sortWay = array(); //排序方式
        if ( is_string($order) ) {
            //id desc, name asc
            if ( ($pos = strpos($order, ',')) !== false ) {
                $order = explode(',', $order);
                foreach($order as $val) {
                    $val = trim(preg_replace('/\s+/', ' ', $val));
                    $arr = explode(' ', $val);
                    if( $arr[0] != '' ) $sortField[] = $arr[0];
                    if ( $arr[1] != '' ) $sortWay[] = strtoupper($arr[1]);
                }
            }

        } else if ( is_array($order) ) {
            foreach ( $order as $key => $value ) {
                $sortField[] = $key;
                if ( $value == 1 ) {
                    $sortWay[] = 'ASC';
                } else if ( $value == -1 ) {
                    $sortWay[] = 'DESC';
                }
            }
        }

        return array('sort_field' => $sortField, 'sort_way' => $sortWay);
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
