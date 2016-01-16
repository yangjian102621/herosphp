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
 * @version 1.2.1
 *-----------------------------------------------------------------------*/

namespace herosphp\core;

use herosphp\bean\Beans;
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
     * action 实例
     * @var Object
     */
    private $actionInstance = null;

    /**
     * 应用程序监听器
     * @var array
     */
    private $listeners = array();

    /**
     * 应用程序唯一实例
     * @var WebApplication
     */
    private static $_INSTANCE = null;

    private function __construct() {

        //初始化应用程序监听器
        $this->listeners = Beans::get(Beans::BEAN_WEBAPP_LISTENER);

    }

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
        //调用生命周期监听器
        if ( !empty($this->listeners) ) {
            foreach ( $this->listeners as $lisener ) {
                $lisener->beforeRequestInit();
            }
        }
        $this->httpRequest = new HttpRequest();
        $this->httpRequest->parseURL();
    }

    /**
     * @see \herosphp\core\interfaces\IApplication::actionInvoke()
     */
    public function actionInvoke()
    {
        //调用生命周期监听器
        if ( !empty($this->listeners) ) {
            foreach ( $this->listeners as $lisener ) {
                $lisener->beforeActionInvoke();
            }
        }

        //加载控制器Action文件
        $module = $this->httpRequest->getModule();
        $action = $this->httpRequest->getAction();
        $method = $this->httpRequest->getMethod();
        $actionDir = APP_PATH."modules/{$module}/action/";
        $actionFile = ucfirst($action).'Action.class.php';
        $filename = $actionDir.$actionFile;
        if ( !file_exists($filename) ) {
            if ( APP_DEBUG ) {
                E("Action file {$filename} not found. ");
            }
        }
        include $filename;
        $className = "\\{$module}\\action\\".ucfirst($action)."Action";
        $this->actionInstance = new $className();

        //调用初始化方法
        if ( method_exists($this->actionInstance, 'C_start') ) {
            $this->actionInstance->C_start();
        }

        //根据动作去找对应的方法
        if ( method_exists($this->actionInstance, $method) ) {
            $this->actionInstance->$method($this->httpRequest);
        } else {
            if ( APP_DEBUG ) {
                E("Method {$className}::{$method} not found!");
            }
        }
    }

    /**
     * @see \herosphp\core\interfaces\IApplication::sendResponse()
     */
    public function sendResponse()
    {
        //调用响应发送前生命周期监听器
        if ( !empty($this->listeners) ) {
            foreach ( $this->listeners as $lisener ) {
                $lisener->beforeSendResponse($this->actionInstance);
            }
        }

        //加载并显示视图
        $this->actionInstance->display($this->actionInstance->getView());

        //调用响应发送后生命周期监听器
        if ( !empty($this->listeners) ) {
            foreach ( $this->listeners as $lisener ) {
                $lisener->afterSendResponse($this->actionInstance);
            }
        }
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
     * @param string $key 配置key
     * @return mixed
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
