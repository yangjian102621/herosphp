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
    }

    /**
     * @see \herosphp\db\interfaces\Idb::connect()
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
     * @see \herosphp\db\interfaces\Idb::query()
     * @throws DBException
     */
    public function query($_query) {

        if ( $this->link == null ) $this->connect();
        if ( DB_ESCAPE ) $_query = addslashes($_query);
        try {
            $_result = $this->link->query($_query);
        } catch ( PDOException $e ) {
            $_exception = new DBException("SQL错误:" . $e->getMessage());
            $_exception->setCode($e->getCode());
            $_exception->setQuery($_query);
            if ( APP_DEBUG ) {
                __print($_query);
            }
            throw $_exception;
        }
        Debug::appendMessage($_query, 'sql');   //添加调试信息
        return $_result;
    }

    /**
     * @see \herosphp\db\interfaces\Idb::insert()
     */
    public function insert($_table, &$_array)
    {
		$_fileds = '';
		$_values = '';
		$_T_fields = $this->getTableFields( $_table );
		foreach ( $_array as $_key => $_val ) {

			//自动过滤掉不存在的字段
			if ( !in_array( $_key, $_T_fields ) ) continue;

			$_fileds .= ( $_fileds=='' ) ? "`{$_key}`" : ", `{$_key}`" ;
			$_values .= ( $_values=='' ) ? "'".$_val."'" : ",'".$_val."'";

		}

		if ( $_fileds !== null ) {
			$_query = "INSERT INTO ".$_table."(" . $_fileds . ") VALUES(" . $_values . ")";

			if ( $this->query( $_query ) != false ){
				$last_insert_id = $this->link->lastInsertId();
                if ( $last_insert_id ) {
                    return $last_insert_id;
                } else {    //ID不是自增的而是手动生成的
                    return true;
                }
			}
		}
        return false;
    }

    /**
     * @see \herosphp\db\interfaces\Idb::replace()
     */
    public function replace($_table, &$_array ) {

        $_fileds = '';
        $_values = '';
        $_T_fields = $this->getTableFields( $_table );
        foreach ( $_array as $_key => $_val ) {

            //自动过滤掉不存在的字段
            if ( !in_array( $_key, $_T_fields ) ) continue;

            $_fileds .= ( $_fileds=='' ) ? "`{$_key}`" : ", `{$_key}`";
            $_values .= ( $_values=='' ) ? "'".$_val."'" : ",'".$_val."'";
        }

        if ( $_fileds !== null ) {
            $_query = "REPLACE INTO ".$_table."(" . $_fileds . ") VALUES(" . $_values . ")";
            if ( $this->query( $_query ) != false )
                return true;
        }
        return false;
    }

    /**
     *  @see \herosphp\db\interfaces\Idb::delete()
     */
    public function delete($_table, $_conditons = null)
    {
        $_sql = "DELETE FROM ".$_table;
        if ( $_conditons ) {
            $_sql .= " WHERE ".$_conditons;
        } else {
            return false;
        }
        $_result = $this->query($_sql);
        if ( $_result ) return true;
        return false;
    }

    /**
     * @see \herosphp\db\interfaces\Idb::getList()
     */
    public function &getList($_query)
    {
        $_result = array();
        $_ret = $this->query( $_query );
        if ( $_ret != false ) {

            while ( ($_rows = $_ret->fetch(PDO::FETCH_ASSOC)) != false )
                $_result[]  = $_rows;
        }
        return $_result;
    }

    /**
     * @see \herosphp\db\interfaces\Idb::getOneRow()
     */
    public function &getOneRow($_query)
    {
        $_result = array();
        $_ret = $this->query( $_query );
        if ( $_ret != false ) {
            $_result = $_ret->fetch(PDO::FETCH_ASSOC);
        }
        return $_result;
    }

    /**
     * @see \herosphp\db\interfaces\Idb::update()
     */
    public function update($_table, &$_array, $_conditons = null)
    {
        if ( !$_conditons ) return false;

        $_T_fields = $this->getTableFields($_table);
        $_keys = '';
        foreach ( $_array as $_key => $_val ) {

            //过滤不存在的字段
            if ( !in_array($_key, $_T_fields) ) continue;
            $_keys .= $_keys == ''? "`{$_key}`='{$_val}'" : ", `{$_key}`='{$_val}'";
        }
        if ( $_keys !== '' ) {
            $_query = "UPDATE " . $_table . " SET " . $_keys . " WHERE ".$_conditons;
            return $this->query($_query);

            //如果没有传入任何字段则默认也是更新成功的
        } else {
            return true;
        }
        return false;
    }

    /**
     * @see \herosphp\db\interfaces\Idb::count()
     */
    public function count($_table, $_conditons = null)
    {
        $_query = "SELECT count(*) as total FROM {$_table}";
        if ( $_conditons ) $_query .= " WHERE ".$_conditons;
        $_result = $this->query($_query);
        $_res = $_result->fetch(PDO::FETCH_ASSOC);
        return $_res['total'];
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
        $_ret = $this->query( $_sql );
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

	/**
	 * @return the $config
	 */
	public function getConfig() {
		return $this->config;
	}

	/**
	 * @param field_type $config
	 */
	public function setConfig($config) {
		$this->config = $config;
	}
}
