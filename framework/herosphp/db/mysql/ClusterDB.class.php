<?php
/*---------------------------------------------------------------------
 * 数据库集群 => 数据库操作服务实现类。
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\db\mysql;

use herosphp\core\Loader;
use herosphp\db\interfaces\ICusterDB;
use herosphp\exception\DBException;
use \PDO;
use \PDOException;

Loader::import('db.interfaces.ICusterDB', IMPORT_FRAME);

/**
 * 多数据库连接操作类
 * @author          yangjian102621@gmail.com
 */
class ClusterDB implements ICusterDB {

    protected static $_READ_POOL = array();     /* 读服务器连接池 */

    protected static $_WRITE_POOL = array();    /* 写服务器连接池 */

    protected $currentReadServer = null;       /* 当前读连接服务器 */

    protected $currentWriteServer = null;       /* 当前写连接服务器 */

    /**
     * 事务的级数，解决事务的嵌套问题
     * @var int
     */
    private $transactions = 0;

    /**
     * 创建一个数据库操作对象,初始化配置参数
     * @param $configs
     */
    public  function __construct( $configs ) {

        if ( !is_array($configs) || empty($configs) )  E("必须传入数据库的配置信息！");

        foreach ( $configs as $value ) {
            if ( $value['serial'] == 'db-write' ) {
                $this->addWriteServer($value);
            } else if ( $value['serial'] == 'db-read' ) {
                $this->addReadServer($value);
            }

        }

    }

    /**
     * @see \herosphp\db\interfaces\ICusterDB::connect()
     * @throws DBException
     */
    public function connect()
    {
        return false;
        //throw new DBException("暂时不支持该方法！");
    }

    /**
     * 创建数据库连接, 本类采用PDO的实现方式
     */
    protected function getDBconnect( $config ) {

        $_dsn="{$config['db_type']}:host={$config['db_host']};dbname={$config['db_name']}";
        try {
            $_pdo = new PDO($_dsn, $config['db_user'], $config['db_pass'], array(PDO::ATTR_PERSISTENT=>false));
            $_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //设置数据库编码，默认使用UTF8编码
            $_charset = $config['db_charset'];
            if ( !$_charset ) $_charset = 'UTF8';
            $_pdo->execute("SET names {$_charset}");
            $_pdo->execute("SET character_set_client = {$_charset}");
            $_pdo->execute("SET character_set_results = {$_charset}");
        } catch ( PDOException $e ) {
            if ( APP_DEBUG ) {
                E("数据库连接失败".$e->getMessage());
            }
        }
        return $_pdo;
    }

    /**
     * 执行一条SQL语句，不同类型的SQL语句发送到不同的服务器去执行。
     * 1. 读的SQL语句发送到读服务器
     * 2. 写入SQL语句发送到写服务器
     * 3. 此方法将抛出异常
     * @see Idb::execute()
     * @throws DBException
     */
    public function execute($_query)
    {
        if ( $this->isReadSQL($_query) ) {      /* 读取数据操作 */
            $_db = $this->selectReadServer();
        } else {            /* 写入数据操作 */
            $_db = $this->selectWriteServer();
        }

        try {
            $_result = $_db->execute($_query);
        } catch ( PDOException $e ) {
            $_exception = new DBException("SQL错误!".$e->getMessage());
            $_exception->setCode($e->getCode());
            $_exception->setQuery($_query);
            throw $_exception;
        }
        return $_result;
    }

    /**
     * @see Idb::query()
     */
    public function query($query)
    {
        $_result = array();
        $_ret = $this->execute($query);
        if ( $_ret != false ) {

            while ( ($_rows = $_ret->fetch(PDO::FETCH_ASSOC)) != false )
                $_result[]  = $_rows;
        }
        return $_result;
    }

    /**
     * @see ICusterDB::insert()
     */
    public function insert($table, $data)
    {
        $_fileds = '';
        $_values = '';
        $_T_fields = $this->getTableFields($table);
        foreach ( $data as $_key => $_val ) {

            //自动过滤掉不存在的字段
            if ( !in_array( $_key, $_T_fields ) ) continue;

            $_fileds .= ( $_fileds=='' ) ? "`{$_key}`" : ", `{$_key}`" ;
            $_values .= ( $_values=='' ) ? "'".$_val."'" : ",'".$_val."'";

        }

        if ( $_fileds != '' ) {
            $_query = "INSERT INTO {$table}(" . $_fileds . ") VALUES(" . $_values . ")";

            if ( $this->query( $_query ) != false ) {
                $last_insert_id = $this->currentWriteServer->lastInsertId();
                if ( $last_insert_id > 0 ) { //返回自增id
                    return $last_insert_id;
                } else {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @see \herosphp\db\interfaces\ICusterDB::replace()
     */
    public function replace($table, $data) {

        $_fileds = '';
        $_values = '';
        $_T_fields = $this->getTableFields($table);
        foreach ( $data as $_key => $_val ) {

            //自动过滤掉不存在的字段
            if ( !in_array( $_key, $_T_fields ) ) continue;

            $_fileds .= ( $_fileds=='' ) ? "`{$_key}`" : ", `{$_key}`";
            $_values .= ( $_values=='' ) ? "'".$_val."'" : ",'".$_val."'";
        }

        if ( $_fileds != '' ) {
            $_query = "REPLACE INTO {$table}(" . $_fileds . ") VALUES(" . $_values . ")";
            if ( $this->execute($_query) != false ) {
                return true;
            }

        }
        return false;
    }

    /**
     * @see Idb::update()
     */
    public function update($table, $data, $condition)
    {
        if ( empty($condition) ) return false;
        $where = MysqlQueryBuilder::buildConditions($condition);

        $_T_fields = $this->getTableFields($table);
        $_keys = '';
        foreach ( $data as $_key => $_val ) {

            //过滤不存在的字段
            if ( !in_array($_key, $_T_fields) ) continue;
            $_keys .= $_keys == ''? "`{$_key}`='{$_val}'" : ", `{$_key}`='{$_val}'";
        }
        if ( $_keys !== '' ) {
            $_query = "UPDATE {$table} SET " . $_keys . " WHERE ".$where;
            $result = $this->execute($_query);
            if ( $result != false ) {
                return $result->rowCount();
            }
        }
        return false;
    }

    /**
     *  @see Idb::delete()
     */
    public function delete($table, $condition)
    {
        if ( !$condition ) return false; //防止误删除所有的数据，所以必须传入删除条件

        $where = MysqlQueryBuilder::buildConditions($condition);

        $sql = "DELETE FROM {$table} WHERE {$where}";
        $result = $this->execute($sql);
        if ( $result ) {
            return $result->rowCount();
        }
        return false;
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
        $items = array();
        $query = MysqlQueryBuilder::getInstance()
            ->table($table)
            ->where($condition)
            ->fields($field)
            ->order($sort)
            ->limit($limit)
            ->group($group)
            ->having($having);

        $result = $this->execute($query->buildQueryString());
        if ( $result != false ) {
            while ( ($row = $result->fetch(PDO::FETCH_ASSOC)) != false ) {
                $items[]  = $row;
            }

        }
        return $items;
    }

    /**
     * @see Idb::findOne()
     */
    public function &findOne($table, $condition=null, $field=null, $sort=null)
    {
        $query = MysqlQueryBuilder::getInstance()
            ->table($table)
            ->where($condition)
            ->fields($field)
            ->order($sort);

        $result = $this->execute($query->buildQueryString());
        if ( $result != false ) {
            return $result->fetch(PDO::FETCH_ASSOC);
        }
        return false;
    }

    /**
     * @see Idb::count()
     */
    public function count($table, $condition=null)
    {
        $sql = "SELECT count(*) as total FROM {$table}";

        if ( $condition != null ) {
            $sql .= " WHERE ".MysqlQueryBuilder::buildConditions($condition);
        }

        $result = $this->execute($sql);
        $res = $result->fetch(PDO::FETCH_ASSOC);
        return $res['total'];
    }

    /**
     * @see \herosphp\db\interfaces\ICusterDB::beginTransaction()
     */
    public function beginTransaction()
    {
        $_db = $this->selectWriteServer();      /* 只有写入服务器需要开启事物 */
        ++$this->transactions;
        if ( $this->transactions == 1 ) {
            $_db->beginTransaction();
        }
    }

    /**
     * @see \herosphp\db\interfaces\ICusterDB::commit()
     */
    public function commit()
    {
        $_db = $this->selectWriteServer();
        if ( $this->transactions == 1 ) {
            $_db->commit();
        }
    }

    /**
     * @see \herosphp\db\interfaces\ICusterDB::rollBack()
     */
    public function rollBack()
    {
        $_db = $this->selectWriteServer();
        if ( $this->transactions == 1 ) {

            $this->transactions = 0;
            $_db->rollBack();

        } else {
            --$this->transactions;
        }

    }

    /**
     * @see \herosphp\db\interfaces\ICusterDB::inTransaction()
     */
    public function inTransaction()
    {
        if ( $this->link == null ) $this->connect();
        $_db = $this->selectWriteServer();
        return $_db->inTransaction();
    }

    /***
     * 获取指定数据表的所有字段
     * @param		string 		$_table		table name
     * @return 		array		fields array of table
     */
    protected function getTableFields( $_table ) {

        $_sql = "SHOW COLUMNS FROM {$_table}";
        $_ret = $this->execute( $_sql );

        $_fields = array();
        if ( $_ret != false ) {
            while ( ($_rows = $_ret->fetch(PDO::FETCH_BOTH)) != false ) {
                $_fields[] = $_rows[0];
            }
        }
        return $_fields;
    }

    /**
     * @see \herosphp\db\interfaces\ICusterDB::addReadServer()
     */
    public function addReadServer($config)
    {
        if ( !empty($config) ) {
            self::$_READ_POOL[] = $config;
        }
    }

    /**
     * 获取读服务器配置池
     */
    public function getReadServer() {
        return self::$_READ_POOL;
    }

    /**
     * 获取写服务器配置池
     */
    public function getWriteServer() {
        return self::$_WRITE_POOL;
    }

    /**
     * @see \herosphp\db\interfaces\ICusterDB::addWriteServer()
     */
    public function addWriteServer($config)
    {
        if ( !empty($config) ) {
            self::$_WRITE_POOL[] = $config;
        }
    }

    /**
     * 判断是否读的sql语句
     */
     public  function isReadSQL( $query ) {

         $query = strtoupper(trim($query));
         return (strpos($query, 'SELECT', 0) === 0);
     }

    /**
     * 选择一个读数据库服务器.
     * 如果数据库还没有连接，则创建连接
     */
    protected function selectReadServer() {

        if ( $this->currentReadServer != null )
            return $this->currentReadServer;
        //创建读数据库连接
        $_config = $this->getReadPloy();
        $this->currentReadServer = $this->getDBconnect($_config);
        return $this->currentReadServer;

    }

    /**
     * 选择一个写数据库服务器.
     * 如果数据库还没有连接，则创建连接
     */
    protected function selectWriteServer() {

        if ( $this->currentWriteServer != null )
            return $this->currentWriteServer;

        //创建写数据库连接
        $_config = $this->getWritePloy();
        $this->currentWriteServer = $this->getDBconnect($_config);

        return $this->currentWriteServer;

    }

    /**
     * 获取一个读数据库服务器选择策略
     */
    protected function getReadPloy() {
        //1.随机策略实现
        return self::$_READ_POOL[mt_rand(0, count(self::$_READ_POOL)-1 )];
    }

    /**
     * 获取一个写数据库服务器选择策略
     */
    protected function getWritePloy() {
        //1.随机策略实现
        return self::$_WRITE_POOL[mt_rand(0, count(self::$_WRITE_POOL)-1 )];
    }

}
