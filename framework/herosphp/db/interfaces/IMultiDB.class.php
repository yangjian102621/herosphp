<?php
namespace modphp\db\interfaces;
/**
 * 多数据库操作服务类接口.
 * @author      yangjian102621@gmail.com
 * @since       2014-06-06
 */

interface IMultiDB extends Idb {

    /**
     * 添加一个读数据库服务器
     * @param       array       数据库服务配置参数
     */
    public  function addReadServer( $config );

    /**
     * 添加一个写数据库服务器
     * @param       array       数据库配置参数
     */
    public function addWriteServer( $config );

}