<?php
/*---------------------------------------------------------------------
 * 单数居库服务器操作的PDO实现
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\db\mysql;

use herosphp\core\Debug;
use herosphp\core\Loader;
use herosphp\db\interfaces\Idb;
use herosphp\exception\DBException;
use \PDO;
use \PDOException;

Loader::import('db.interfaces.Idb', IMPORT_FRAME);
class SingleDB implements Idb {

    /**
     * PDO 数据库连接实例
     * @var \PDO
     */
    private $link;

    /**
     * 数据库配置参数
     * @var array
     */
    private $config = array();

    /**
     * 事务的级数，解决事务的嵌套问题
     * @var int
     */
    private $transactions = 0;

    /**
     * 创建一个数据库操作对象,初始化配置参数
     * @param $config
     */
    public  function __construct( $config ) {

        if ( !is_array($config) || empty($config) ) E("必须传入数据库的配置信息！");
        $this->config = $config;

        $this->connect(); //连接数据库
    }

    /**
     * @throws DBException
     * @return Resource
     */
    public function connect()
    {
        if ( $this->link != null ) return true;
        $_config = $this->config;
        $_dsn="{$_config['db_type']}:host={$_config['db_host']};port={$_config['db_port']};dbname={$_config['db_name']}";
        try {
            $this->link = new PDO($_dsn, $_config['db_user'], $_config['db_pass'], array(PDO::ATTR_PERSISTENT=>false));
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            //设置数据库编码，默认使用UTF8编码
            $_charset = $_config['db_charset'];
            if ( !$_charset ) $_charset = 'UTF8';
            $this->link->query("SET names {$_charset}");
            $this->link->query("SET character_set_client = {$_charset}");
            $this->link->query("SET character_set_results = {$_charset}");

        } catch (PDOException $e ) {
            if ( APP_DEBUG ) {
                E("数据库连接失败".$e->getMessage());
            }
        }
        return $this->link;
    }

    /**
     * @see Idb::excute()
     * @param string $sql
     * @return \PDOStatement
     * @throws DBException
     */
    public function excute($sql) {
        if ( $this->link == null ) $this->connect();
        if ( DB_ESCAPE ) $sql = addslashes($sql);
        try {
            $result = $this->link->query($sql);
        } catch ( PDOException $e ) {
            $exception = new DBException("SQL错误:" . $e->getMessage());
            $exception->setCode($e->getCode());
            $exception->setQuery($sql);
            if ( APP_DEBUG ) {
                __print($sql);
            }
            throw $exception;
        }
        Debug::appendMessage($sql, 'sql');   //添加调试信息
        return $result;
    }

    /**
     * @see Idb::query()
     */
    public function query($query)
    {
        $_result = array();
        $_ret = $this->excute($query);
        if ( $_ret != false ) {

            while ( ($_rows = $_ret->fetch(PDO::FETCH_ASSOC)) != false )
                $_result[]  = $_rows;
        }
        return $_result;
    }

    /**
     * @see Idb::insert()
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
                $last_insert_id = $this->link->lastInsertId();
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
     * @see Idb::replace()
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
            if ( $this->excute($_query) != false ) {
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
        $where = MysqlQueryBuilder::getInstance()->where($condition)->buildConditions();

        $_T_fields = $this->getTableFields($table);
        $_keys = '';
        foreach ( $data as $_key => $_val ) {

            //过滤不存在的字段
            if ( !in_array($_key, $_T_fields) ) continue;
            $_keys .= $_keys == ''? "`{$_key}`='{$_val}'" : ", `{$_key}`='{$_val}'";
        }
        if ( $_keys !== '' ) {
            $_query = "UPDATE {$table} SET " . $_keys . " WHERE ".$where;
            $result = $this->excute($_query);
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

        $where = MysqlQueryBuilder::getInstance()->where($condition)->buildConditions();

        $sql = "DELETE FROM {$table} WHERE {$where}";
        $result = $this->excute($sql);
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

        $result = $this->excute($query->buildQueryString());
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
        $item = array();
        $query = MysqlQueryBuilder::getInstance()
            ->table($table)
            ->where($condition)
            ->fields($field)
            ->order($sort);

        $result = $this->excute($query->buildQueryString());
        if ( $result != false ) {
            $item = $result->fetch(PDO::FETCH_ASSOC);
        }
        return $item;
    }

    /**
     * @see Idb::count()
     */
    public function count($table, $condition=null)
    {
        $sql = "SELECT count(*) as total FROM {$table}";

        if ( $condition != null ) {
            $sql .= " WHERE ".MysqlQueryBuilder::getInstance()->buildConditions($condition);
        }

        $result = $this->excute($sql);
        $res = $result->fetch(PDO::FETCH_ASSOC);
        return $res['total'];
    }

    /**
     * @see \herosphp\db\interfaces\Idb::beginTransaction()
     */
    public function beginTransaction()
    {
        if ( $this->link == null ) {
            $this->connect();
        }
        ++$this->transactions;

        if ( $this->transactions == 1 ) {
            $this->link->beginTransaction();
        }


    }

    /**
     * @see \herosphp\db\interfaces\Idb::commit()
     */
    public function commit()
    {
        if ( $this->link == null ) {
            $this->connect();
        }
        if ( $this->transactions == 1 ) {
            $this->link->commit();
        }

        --$this->transactions;
    }

    /**
     * @see \herosphp\db\interfaces\Idb::rollBack()
     */
    public function rollBack()
    {
        if ( $this->link == null ) {
            $this->connect();
        }
        if ( $this->transactions == 1 ) {

            $this->transactions = 0;
            $this->link->rollBack();

        } else {
            --$this->transactions;
        }

    }

    /**
     * @see \herosphp\db\interfaces\Idb::inTransaction()
     */
    public function inTransaction()
    {
        if ( $this->link == null ) $this->connect();
        return $this->link->inTransaction();
    }

    /***
     * 获取指定数据表的所有字段
     * @param		string 		$_table		table name
     * @return 		array		fields array of table
     */
    protected function getTableFields( $_table ) {

        $_sql = "SHOW COLUMNS FROM {$_table}";
        $_ret = $this->excute( $_sql );
        $_fields = array();
        if ( $_ret != false ) {
            while ( ($_rows = $_ret->fetch()) != false ) {
                $_fields[] = $_rows[0];
            }
        }
        return $_fields;
    }

    /**
     * 释放资源
     */
    public function __destruct() {

        if ( $this->link ) $this->link = null;
    }

}
