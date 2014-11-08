<?php
/*********************************************************************************
 * HerosPHP框架系统类
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-2013 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @completed 	2013.04.05
 * @lastUpdate	2013.04.15
 * ******************************************************************************/
include ROOT.DIR_OS.'libs'.DIR_OS.'functions.php';      //包含全局函数
class Herosphp {
    
    private static $_IMPORTED_FILES = array();      //一导入的class文件
    
    public static $_LIB_CLASS = array();      //框架的基本类库
    
    private static $_APP_CONFIG = array();    //当前应用的配置信息
    
    public static $_APP_NAME = DEFAULT_APP;  //当前应用的名称   
    
    public static function run() {
        
        //初始化系统配置
        self::import('config:common.SysCfg');
        SysCfg::init();  
        
        date_default_timezone_set(SysCfg::$time_zone);  //设置默认时区  
        
        //初始化缓存配置
        self::import('config:common.CacheCfg');
        CacheCfg::init();
        
        //入口验证变量，防止用户直接访问其他页面
        !defined("SYS_DOOR") && define("SYS_DOOR", TRUE);
        
        self::__loadBaseLib();      //初始化核心类数组
        
        error_reporting(E_ALL);   //配置错误输出
        set_error_handler(array("Debug", 'customError')); //设置捕获系统异常
        //处理错误， 开启debug模式
        if ( SysCfg::$debug == 1 ) {
            Debug::start();                               //开启脚本计算时间
        } 
        //开启错误日志模式
        else {
            //ini_set("display_errors", "Off");
            //ini_set("log_errors", "On");
            //ini_set("error_log", "logs/error_log.txt");
        }
        
        //解析url,采用pathinfo的访问模式，如果想用常规的访问模式，可传入参数__NORMAL_REQUEST__
        HttpRequest::parseURL(__PATH_INFO_REQUEST__);
        //引入模块的配置文档
        self::$_APP_CONFIG = include HttpRequest::$_request['app_config'].DIR_OS.'config.php';
		self::$_APP_CONFIG = array_merge(SysCfg::$site_config, self::$_APP_CONFIG);		//合并系统设置
        
        /* 配置模板解析类的配置参数 */
        $_request = HttpRequest::getRequest();
        $GLOBALS['temp_config'] = array(
            "tpl_dir"       => $_request['app_home'].DIR_OS.$_request['module'].DIR_OS.
                            SysCfg::$temp_dir.DIR_OS.self::getAppConfig('temp_dir'),   //模板目录
            "comp_dir"      => ROOT.DIR_OS.SysCfg::$compile_dir.DIR_OS.$_request['app_name'],    //编译目录
            "user_rules"    => SysCfg::$user_rules,                 //用户自定义的模板编译规则
            "cache"         => self::getAppConfig('cache_enable')          //是否开启模板缓存                          
        );
        
        //找到对应的控制器文件
        $_action_file = HttpRequest::$_request['app_home'].DIR_OS.HttpRequest::$_request['module'].DIR_OS.
                        SysCfg::$action_dir.DIR_OS.ucfirst(HttpRequest::$_request['action'])."Action.class.php";
        if ( file_exists($_action_file) ) {
            include $_action_file;
            $_className = ucfirst(HttpRequest::$_request['action']).'Action';
            $_action = new $_className(); 
            $_action->start();
        } else {
            trigger_error("您要进行的操作[".HttpRequest::$_request['module']."][".HttpRequest::$_request['action']."][".HttpRequest::$_request['method']."]不存在！");
        }
        
        //输出错误信息
        if ( SysCfg::$debug == 1 ) {
            Debug::printMessage();
        }
    }
	
	/**
	 * 获取当前应用的配置信息
	 * @param		string		$_key
	 */
	public static function getAppConfig( $_key ) {
		
		return self::$_APP_CONFIG[$_key];
	}
    
    /**
     * 加载一个类或者加载一个包
     * 如果加载的包中有子文件夹不进行循环加载
     * 参数格式说明：'home:article.model.articleModel'
     * home 当前注册的应用名称或者库文件根目录，应用名称与路径信息用‘:’号分隔
     * article.model.articleModel 相对的路径信息
     * 如果不填写应用名称 ，例如‘article.model.articleModel’，那么加载路径则相对于默认的应用路径
     *
     * 加载一个类的参数方式：'article.model.articleModel'
     * 加载一个包的参数方式：'article.service.*'
     * 
     * @param string $_file_path | 文件路径信息 或者className
     * @return booleam
     */
    public static function import( $_file_path ) {
    	
         if ( !$_file_path ) return;
		 //每个文件只包含一次
         if ( isset(self::$_IMPORTED_FILES[$_file_path]) ) return TRUE;	
         $_home = self::$_APP_NAME;         //默认使用当前应用的目录为根目录
         if ( ($_pos_1 = strpos($_file_path, ':')) !== FALSE ) $_home = substr($_file_path, 0, $_pos_1);
         if ( ($_pos_2 = strrpos($_file_path, '.')) !== FALSE ) {
             $_file = substr($_file_path, $_pos_2+1);
         } else return FALSE;
         
         if ( $_pos_1 !== FALSE ) {
             $_path = substr($_file_path, $_pos_1+1, ($_pos_2-$_pos_1));
         } else {
             $_path = str_replace('.', DIR_OS, substr($_file_path, 0, $_pos_2+1));
         }
         $_path = ROOT.DIR_OS.$_home.DIR_OS.str_replace('.', DIR_OS, $_path);
         
         if ( $_file == '*' && file_exists($_path) ) {     //加载包
             chdir($_path);
             $_class_files = glob('*.class.php');
             foreach ( $_class_files as $_filename ) {
                 include $_path.$_filename;
                 self::$_IMPORTED_FILES[$_file_path] = 1;
             }
         } else {
             $_filename = $_path.$_file.'.class.php';
             if ( file_exists($_filename) ) {
                 include $_path.DIR_OS.$_file.'.class.php';
                 self::$_IMPORTED_FILES[$_file_path] = 1;
             }
             else trigger_error("您要包含的文件[{$_filename}]不存在！");
         }
         return TRUE;
		 
    }

    /**
     * 加载核心层库函数
     * 
     * @return void
     */
    private static function __loadBaseLib() {
        self::$_LIB_CLASS = array(
            'CacheFactory'      => 'cache.CacheFactory', 
            'CompFactory'       => 'comp.CompFactory', 
            'Action'            => 'core.Action', 
            'Controller'        => 'core.Controller', 
            'Debug'             => 'core.Debug', 
            'Template'          => 'core.Template', 
            'HttpRequest'       => 'core.HttpRequest', 
            'DBFactory'         => 'db.DBFactory', 
            'MysqlModelDao'     => 'model.MysqlModelDao', 
            'ChartFactory'      => 'utils.ChartFactory', 
            'FileUpload'        => 'utils.FileUpload', 
            'Filter'            => 'utils.Filter', 
            'Image'             => 'utils.Image', 
            'Page'              => 'utils.Page', 
            'PHPZip'            => 'utils.PHPZip', 
            'Utils'             => 'utils.Utils', 
            'VerifyCode'        => 'utils.VerifyCode',
            'AdmincommonAction' => 'core.AdmincommonAction',
            'HomecommonAction'  => 'core.HomecommonAction',
            'AjaxResult'        => 'public.AjaxResult');
    }
    
}

//自动加载核心类
function __autoload( $_classname ) {
    if ( isset(Herosphp::$_LIB_CLASS[$_classname]) ) Herosphp::import('libs:'.Herosphp::$_LIB_CLASS[$_classname]);
}
?>