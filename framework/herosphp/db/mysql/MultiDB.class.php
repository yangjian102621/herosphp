<?php
namespace modphp\db;

use modphp\db\interfaces\ImultiDB;
use \PDO;
use \PDOException;

/**
 * 多数据库连接操作类
 * @author          yangjian102621@gmail.com
 */
class MultiDB implements ImultiDB {

    protected static $_READ_POOL = array();     /* 读服务器连接池 */

    protected static $_WRITE_POOL = array();    /* 写服务器连接池 */

    protected $currentReadServer = NULL;       /* 当前读连接服务器 */

    protected $currentWriteServer = NULL;       /* 当前写连接服务器 */

    /**
     * 创建一个数据库操作对象
     * 采用单厂模式，私有化构造方法
     */
    public  function __construct() {}

    /**
     * connect database
     * @throws DBException
     * @return      resource of database connection.
     */
    public function connect()
    {
        throw new DBException("暂时不支持该方法！");
    }

    /**
     * 执行一条SQL语句，不同类型的SQL语句发送到不同的服务器去执行。
     * 1. 读的SQL语句发送到读服务器
     * 2. 写入SQL语句发送到写服务器
     * 3. 此方法将抛出异常
     * @param        string $_query query string
     * @throws DBException
     */
    public function query($_query)
    {
        if ( $this->isReadSQL($_query) ) {      /* 读取数据操作 */
            $_db = $this->selectReadServer();
        } else {            /* 写入数据操作 */
            $_db = $this->selectWriteServer();
        }

        try {
            $_result = $_db->query($_query);
        } catch ( PDOException $e ) {
            $_exception = new DBException("SQL错误");
            $_exception->setCode($e->getCode());
            $_exception->setQuery($_query);
            throw $_exception;
        }

        return $_result;
    }

    /**
     * insert a record to database.
     * @param        string $_table table name
     * @param         array $_array
     * @return        int        return the last insert id
     */
    public function insert($_table, &$_array)
    {
        $_fileds = '';
        $_values = '';
        $_T_fields = $this->getTableFields( $_table );
        foreach ( $_array as $_key => $_val ) {

            //自动过滤掉不存在的字段
            if ( !in_array( $_key, $_T_fields ) ) continue;

            $_fileds .= ( $_fileds=='' ) ? $_key : ',' . $_key;
            $_values .= ( $_values=='' ) ? "'".$_val."'" : ",'".$_val."'";
        }

        if ( $_fileds !== NULL ) {
            $_query = "INSERT INTO ".$_table."(" . $_fileds . ") VALUES(" . $_values . ")";
            if ( $this->query( $_query ) != FALSE )
                return $this->currentWriteServer->lastInsertId();
        }
        return FALSE;
    }

    /**
     * insert a record, if the record exists update it.
     * @param $_table
     * @param $_array
     * @return      boolean
     */
    public function replace( $_table, &$_array ) {

        $_fileds = '';
        $_values = '';
        $_T_fields = $this->getTableFields( $_table );
        foreach ( $_array as $_key => $_val ) {

            //自动过滤掉不存在的字段
            if ( !in_array( $_key, $_T_fields ) ) continue;

            $_fileds .= ( $_fileds=='' ) ? $_key : ',' . $_key;
            $_values .= ( $_values=='' ) ? "'".$_val."'" : ",'".$_val."'";
        }

        if ( $_fileds !== NULL ) {
            $_query = "REPLACE INTO ".$_table."(" . $_fileds . ") VALUES(" . $_values . ")";
            if ( $this->query( $_query ) != FALSE )
                return TRUE;
        }
        return FALSE;
    }

    /**
     * delete a record from table.
     * @param        string $_table table name
     * @param        string $_conditons query condition.
     * @return        mixed
     */
    public function delete($_table, $_conditons = NULL)
    {
        $_sql = "DELETE FROM ".$_table;
        if ( $_conditons != NULL ) $_sql .= " WHERE ".$_conditons;
        $_result = $this->query($_sql);
        if ( $_result ) return $_result->rowCount();
        return FALSE;
    }

    /**
     * Get a list of data records.
     * @param           $_query    the query string
     * @param int|type $_type type of array to the result
     * @return          array
     */
    public function &getItems($_query, $_type = PDO::FETCH_ASSOC)
    {
        $_result = array();
        $_ret = $this->query( $_query );
        if ( $_ret != FALSE ) {

            while ( ($_rows = $_ret->fetch($_type)) != FALSE )
                $_result[]  = $_rows;
        }
        return $_result;
    }

    /**
     * get one data records
     * @param        string $_query query string
     * @param       int|type $_type type of array to the result
     * @return        array
     */
    public function &getItem($_query, $_type = PDO::FETCH_ASSOC)
    {
        $_result = array();
        $_ret = $this->query( $_query );
        if ( $_ret != FALSE ) {
            $_result = $_ret->fetch($_type);
        }
        return $_result;
    }

    /**
     * 更新一条记录
     * @param    string $_table table name
     * @param    array $_array data array  name => value
     * @param    string $_conditons query conditions.
     * @return    int
     */
    public function update($_table, &$_array, $_conditons)
    {
        $_T_fields = $this->getTableFields($_table);
        $_keys = '';
        foreach ( $_array as $_key => $_val ) {

            //过滤不存在的字段
            if ( !in_array($_key, $_T_fields) ) continue;
            $_keys .= $_keys == ''? "{$_key}='{$_val}'" : ", {$_key}='{$_val}'";
        }
        if ( $_keys !== '' ) {
            $_query = "UPDATE " . $_table . " SET " . $_keys . " WHERE ".$_conditons;
            return $this->query( $_query );
        }
        return FALSE;
    }

    /**
     * get total records rows number.(获取总记录数)
     * @param        string $_table table name
     * @param        string $_conditons query conditions
     */
    public function count($_table, $_conditons=NULL)
    {
        $_query = "SELECT count(*) as total FROM {$_table}";
        if ( $_conditons ) $_query .= " WHERE ".SQL::createCondition($_conditons);
        $_result = $this->query($_query);
        $_res = $_result->fetch(PDO::FETCH_ASSOC);
        return $_res['total'];
    }

    /**
     * begin transaction (事物开启)
     */
    public function beginTransaction()
    {
        $_db = $this->selectWriteServer();      /* 只有写入服务器需要开启事物 */
        $_db->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $_db->beginTransaction();
    }

    /**
     * commit transaction (事物提交)
     */
    public function commit()
    {
        $_db = $this->selectWriteServer();
        $_db->commit();
        $_db->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    /**
     * roll back (事物回滚)
     */
    public function rollBack()
    {
        $_db = $this->selectWriteServer();
        $_db->rollBack();
    }

    /***
     * 获取指定数据表的所有字段
     * @param		string 		$_table		table name
     * @return 		array		fields array of table
     */
    protected function getTableFields( $_table ) {

        $_sql = "SHOW COLUMNS FROM {$_table}";
        $_ret = $this->query( $_sql );

        $_fields = array();
        if ( $_ret != FALSE ) {
            while ( ($_rows = $_ret->fetch(PDO::FETCH_BOTH)) != FALSE ) {
                $_fields[] = $_rows[0];
            }
        }
        return $_fields;
    }

    /**
     * 添加一个读数据库服务器
     * @param       array       数据库服务配置参数
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
     * 添加一个写数据库服务器
     * @param       array       数据库配置参数
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

        if ( $this->currentReadServer != NULL )
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

        if ( $this->currentWriteServer != NULL )
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

    /**
     * 创建数据库连接, 本类采用PDO的实现方式
     */
    protected function getDBconnect( $config ) {

        $_dsn="{$config['db_type']}:host={$config['db_host']};dbname={$config['db_name']}";
        try {
            $_pdo = new PDO($_dsn, $config['db_user'], $config['db_pass'], array(PDO::ATTR_PERSISTENT=>true));
            $_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //设置数据库编码，默认使用UTF8编码
            $_charset = $config['db_charset'];
            if ( !$_charset ) $_charset = 'UTF8';
            $_pdo->query("SET names {$_charset}");
            $_pdo->query("SET character_set_client = {$_charset}");
            $_pdo->query("SET character_set_results = {$_charset}");
        } catch ( PDOException $e ) {
            $_exception = new DBException("数据库连接失败".$e->getMessage());
            $_exception->setCode($e->getCode());
            throw $_exception;
        }
        return $_pdo;
    }

}
?>