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
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/
namespace herosphp\core;

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
    private $requestUrl;

    /**
     * 请求参数
     * @var array
     */
    private $parameters = array();

    /**
     * 解析url成pathinfo形式，并获取参数， 如：
     * /index.php/home/aricle/list/index/?mid=3&id=100
     * @param     int         $_flag          访问模式
     */
    public static function parseURL( $_flag = __PATH_INFO_REQUEST__ ) {

        $_path_info = parse_url($_SERVER['REQUEST_URI']);
        $_url = $_path_info['path'];
        if ( ($_pos = strpos($_url, '.html') ) !== FALSE ) $_url = substr($_url, 0, $_pos);
        switch ( $_flag ) {
            case __PATH_INFO_REQUEST__ :    //path info 访问模式
                if ( ($_pos_1 = strpos($_url, '.php')) !== FALSE ) $_path = substr($_url, $_pos_1+5);
                else $_path = substr($_url, 1);

                $_path_info = explode('/', $_path);
                if ( isset($_path_info[1]) && $_path_info[1] == SysCfg::$static_dir ) return;      //静态文件直接访问,不需要解析
                self::$_request['app_name'] = (isset($_path_info[0]) && $_path_info[0] !='') ? $_path_info[0] : DEFAULT_APP;
                self::$_request['module'] = (isset($_path_info[1]) && $_path_info[1] !='') ? $_path_info[1] : SysCfg::$dft_module;
                self::$_request['action'] = (isset($_path_info[2]) && $_path_info[2] !='') ? $_path_info[2] : SysCfg::$dft_action;
                self::$_request['method'] = (isset($_path_info[3]) && $_path_info[3] !='') ? $_path_info[3] : SysCfg::$dft_method;
                break;

            case __NORMAL_REQUEST__ :   //常规访问模式
                self::$_request['app_name'] = isset($_GET['app_name']) ? trim($_GET['app_name']) : DEFAULT_APP;
                self::$_request['module'] = isset($_GET['module']) ? $_GET['module'] : SysCfg::$dft_module;
                self::$_request['action'] = isset($_GET['action']) ? $_GET['action'] : SysCfg::$dft_action;
                self::$_request['method'] = isset($_GET['method']) ? $_GET['method'] : SysCfg::$dft_method;
                break;
        }

        self::$_request['app_home'] = ROOT.DIR_OS.self::$_request['app_name'];
        //当前应用的配置文件目录
        self::$_request['app_config'] = ROOT.DIR_OS.SysCfg::$config_dir.DIR_OS.self::$_request['app_name'];
        Herosphp::$_APP_NAME = self::$_request['app_name'];         //初始化当前app名称

        //初始化用户配置信息
        self::$_config = include ROOT.DIR_OS.SysCfg::$config_dir.DIR_OS.'common'.DIR_OS.'common.config.php';
    }

    public function getParameter() {

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
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param string $requestUrl
     */
    public function setRequestUrl($requestUrl)
    {
        $this->requestUrl = $requestUrl;
    }

    /**
     * @return string
     */
    public function getRequestUrl()
    {
        return $this->requestUrl;
    }

}
?>