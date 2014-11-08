<?php
/**
 * 控制器基类, 所有的控制器类都必须继承此类。 
 * 每个操作对应一个方法。
 * *******************************************************************************
 * 许可声明：此为专门为网络星空PHP高性能建站班级量身定制的"轻量级"PHP框架
 * *******************************************************************************
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。           	
 * @author 	yangjian<yangjian102621@gmail.com>
 * @completed 	2013.04.05
 * @last-update	2013.04.05
 * ******************************************************************************/
class Controller extends Template {
    
    private $_view = NULL;          //视图名称

	/**
     * 程序主方法
     * @param 	 array()	$_tpl_config    模板解析配置参数
	 */
	public function start( $_tpl_config = NULL  ) {
		//过滤非法请求，必须通过唯一入口文件进入控制器
		if ( !SYS_DOOR ) die ("Bad Request!");
		//初始化模板类(Tempalte.class.php)
		parent::__construct($_tpl_config);
		
		//默认调用后程序初始化方法init
		if ( method_exists($this, "init") ) {
			$this->init();
		} 
		//根据对应的Action动作调用对应的方法
		$_method = HttpRequest::$_request['method'];
		if ( $_method != '' && method_exists($this, $_method) ) {
			$this->$_method();
		} else {
			global $_action_file;
			trigger_error("找不到对应的动作方法{$_method}, 请在相应的控制器{$_action_file}中,添加该方法。");
			//error log
		}
        
        //自动调用视图显示方法
        $this->BeforeShowView();
	}
    
    /**
     * 设置视图模板
     * @param       string      $_tpl_name      模板名称
     */
    public function setView( $_tpl_name ) {
        $this->_view = $_tpl_name;
    }
    
    //执行动作之后视图显示之前的操作
    public function BeforeShowView() {
    	
        $this->display($this->_view);
    
	}
	
	/* 
	 * 用于在控制器中进行位置重定向
	 * @param	string	$_url	重定向的目标页面
	 */
	public function location( $_url ){
		if ( isset($_url) ) header("Location:{$_url}");
	}
    
    //获取上一步操作的地址
    public function getReferer() {
        return $_SERVER['HTTP_REFERER'];
    }
	
}
?>
