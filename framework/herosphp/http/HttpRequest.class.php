<?php
/*---------------------------------------------------------------------
 * http 请求解析类, 将url解析成合法格式如下
 * http://www.herosphp.com/app/module-action-method.html?a1=1&a2=2
 * @package herosphp\core
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/
namespace herosphp\http;

use herosphp\core\Loader;

class HttpRequest {

    /**
     * 本次请求访问的模块
     * @var string
     */
    private $module;

    /**
     * 本次请求访问的action
     * @var string
     */
    private $action;

    /**
     * 本次请求调用的主方法
     * @var string
     */
    private $method;

    /**
     * 本次请求的url
     * @var string
     */
    private $requestUri;

    /**
     * 上次请求的url
     * @var
     */
    private $referer;

    /**
     * 请求参数
     * @var array
     */
    private $parameters = array();

    public function __construct() {

        $this->requestUri = $_SERVER['REQUEST_URI'];
        $this->referer = $_SERVER['HTTP_REFERER'];

    }

    /**
     * 解析url成pathinfo形式，并获取参数
     * http://www.herosphp.my/admin/member-login-index.html?id=12
     */
    public function parseURL() {

        $sysConfig = Loader::config();
        $defaultUrl = $sysConfig['default_url'];
        $urlInfo = parse_url($this->requestUri);
        if ( $urlInfo['path'] ) {
            $filename = str_replace(EXT_URI, '', $urlInfo['path']);
            $pathInfo = explode('/', $filename);
            if ( isset($pathInfo[1]) ) {
                $actionMap = explode(ACMAP_SEP, $pathInfo[1]);
                if ( $actionMap[0] ) $this->setModule($actionMap[0]);
                if ( $actionMap[1] ) $this->setAction($actionMap[1]);
                if ( $actionMap[2] ) $this->setMethod($actionMap[2]);
            }

            //提取参数
            if ( isset($pathInfo[2]) ) {
                $params = explode(PARAM_SEP, $pathInfo[2]);
                for ( $i = 0; $i < count($params); $i++ ) {
                    if ( $i % 2 == 0 ) {
                        $_GET[$params[$i]] = $params[$i+1];
                    }
                }
            }

        }

        //如果没有任何参数，则访问默认页面。如http://www.herosphp.my这种格式
        if ( !$this->module ) $this->setModule($defaultUrl['module']);
        if ( !$this->action ) $this->setAction($defaultUrl['action']);
        if ( !$this->method ) $this->setMethod($defaultUrl['method']);

        $this->setParameters($_GET + $_POST);
    }

    /**
     * Get a parameter's value.
     * @param string $name
     * 参数名称
     * @param $func_str
     * 函数名称，参数需要用哪些函数去处理
     * @param boolean $setParam 是否重置参数
     * @return int|string
     */
    public function getParameter( $name, $func_str=null, $setParam=false ) {

        if ( !$func_str ) return $this->parameters[$name];

        $funcs = explode("|", $func_str);
        $args = $this->parameters[$name];
        foreach ( $funcs as $func ) {
            $args = call_user_func($func, $args);
        }
        if ( $setParam ) {
            $this->parameters[$name] = $args;
        }
        return $args;

    }

    /**
     * @param string $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $method
     */
    public function setMethod($method)
    {
        $this->method = $method;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * 添加参数
     * @param $name
     * @param $value
     */
    public function addparmeter( $name, $value ) {
        if ( $name && $value )
            $this->parameters[$name] = $value;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $url
     */
    public function setRequestUri($url)
    {
        $this->requestUri = $url;
    }

    /**
     * @return string
     */
    public function getRequestUri()
    {
        return $this->requestUri;
    }

    /**
     * @param mixed $referer
     */
    public function setReferer($referer)
    {
        $this->referer = $referer;
    }

    /**
     * @return mixed
     */
    public function getReferer()
    {
        return $this->referer;
    }

}
?>