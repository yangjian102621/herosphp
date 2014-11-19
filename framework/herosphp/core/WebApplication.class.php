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
use herosphp\http\HttpRequest;

Loader::import('core.interfaces.IApplication', IMPORT_FRAME);

class WebApplication implements IApplication {

    /**
     * http 请求对象
     * @var \herosphp\http\HttpRequest
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
     * @param array 系统配置信息
     */
    public function execute( $configs ) {

        $this->setConfigs($configs);
        $this->requestInit();

        //invoker 方法调用
        $this->actionInvoke();

        //发送响应
        $this->sendResponse();

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
        $this->httpRequest = new HttpRequest();
        $this->httpRequest->parseURL();
    }

    /**
     * @see \herosphp\core\interfaces\IApplication::actionInvoke()
     */
    public function actionInvoke()
    {

        //加载控制器Action文件


    }

    /**
     * @see \herosphp\core\interfaces\IApplication::sendResponse()
     */
    public function sendResponse()
    {
        // TODO: Implement sendResponse() method.
    }

    /**
     * @param array $configs
     */
    public function setConfigs($configs)
    {
        $this->configs = $configs;
    }

    /**
     * @return array
     */
    public function getConfigs()
    {
        return $this->configs;
    }

    /**
     * 获取指定key的配置值
     * @param $key 配置key
     */
    public function getConfig( $key ) {
        return $this->configs[$key];
    }

    /**
     * @param \herosphp\http\HttpRequest $httpRequest
     */
    public function setHttpRequest($httpRequest)
    {
        $this->httpRequest = $httpRequest;
    }

    /**
     * @return \herosphp\http\HttpRequest
     */
    public function getHttpRequest()
    {
        return $this->httpRequest;
    }

}