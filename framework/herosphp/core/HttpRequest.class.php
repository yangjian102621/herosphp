<?php
/**
 * http 请求解析类, 将url解析成合法格式如下：
 * http://www.herosphp.com/     home       /    article     /   index   /   index   /   ?id=100
 * +----------------------|----app 名称-----|---module-------|---action--|---mehtod---|---args--|
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-2013 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @completed 	2013.04.05
 * @lastUpdate	2013.04.15
 * ******************************************************************************/
 class HttpRequest {
     
    /**
     * @var		http 请求信息 
     */
    public static $_request = array();
	
	/**
	 * @var		用户的配置信息 
	 */
	public static $_config = array();
    
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

     /* 获取用户配置信息 */
     public static function getConfig( $_key ) {

         return self::$_config[$_key];

     }

     /* 获取request 信息 */
     public static function getRequest() {

         return self::$_request;

     }

 }
?>