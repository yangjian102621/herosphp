<?php
/*---------------------------------------------------------------------
 * HerosPHP 应用程序实例类,单例模式
 *  @package herosphp\core
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\core;

use herosphp\core\interfaces\IApplication;

class WebApplication implements IApplication {

    /**
     * http 请求对象
     * @var \herosphp\core\HttpRequest
     */
    private $httpRequest;

    /**
     * 系统配置信息
     * @var array
     */
    private $configs = array();

    /**
     * 应用程序唯一实例
     * @var WebApplication
     */
    private static $_INSTANCE = null;

    private function __construct() {}

    /**
     * 执行应用程序
     */
    public function execute() {

    }

    public static function getInstance() {

        if ( self::$_INSTANCE == null ) {
            self::$_INSTANCE = new self();
        }
        return self::$_INSTANCE;
    }

    /**
     * @see \herosphp\core\interfaces\IApplication::requestInit()
     */
    public function requestInit()
    {

    }

    /**
     * @see \herosphp\core\interfaces\IApplication::actionInvoke()
     */
    public function actionInvoke()
    {
        // TODO: Implement actionInvoke() method.
    }

    /**
     * @see \herosphp\core\interfaces\IApplication::sendResponse()
     */
    public function sendResponse()
    {
        // TODO: Implement sendResponse() method.
    }


} 