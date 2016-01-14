<?php
/*---------------------------------------------------------------------
 * 数据库集群 => 数据库操作服务类接口。
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\db\interfaces;
use herosphp\core\Loader;

Loader::import('db.interfaces.Idb', IMPORT_FRAME);
interface ICusterDB extends Idb {

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