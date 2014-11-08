<?php
/**
 * Action class. <br />
 * 此类封装了一些控制器常用的方法和变量, 控制器可以直接继承此类来快速开发。<br />
 * 如果觉得臃肿或者觉得不需要，可以直接继承Controller基类 <br />
 *
 * @author			yangjian<yangjian102621@gmail.com>
 * @since			2013.06.15
 */
class Action extends Controller {
	
	//fields to query
	protected $_fields = "*";
	//conditions of query
	protected $_conditions = NULL;
	//order way of records
	protected $_order = NULL;
    //每页显示数
    protected $_pagesize = 20;
    //每边输出页码数
    protected$_pageout = 2;
    //数据模型名称
    protected $_modelName;
    //应用的公共模块
    protected $_common_module = 'common';
    //当前模块的首页操作
    protected $_index_url;
    //当前模块静态资源根目录
    protected $_res_url;
    //当前应用的静态资源的根目录
    protected $_app_res_url;
    //当前应用的根url
    protected $_app_url;
    //公共资源地址
    protected $_public_url;
    
	/**
	 * The initialization method 
	 */
	protected function init() {
		
		$this->initVars();
	}
	
	//initialization the variables
	protected function initVars() {
		
		$this->_index_url = SysCfg::$server_url.'/'.HttpRequest::$_request['app_name'].'/'.HttpRequest::$_request['module'].'/'.HttpRequest::$_request['action'];
		$this->_app_res_url = SysCfg::$static_url.'/'.SysCfg::$static_dir.'/'.HttpRequest::$_request['app_name'];
		//$this->_res_url = $this->_app_res_url.'/'.HttpRequest::$_request['module'];
        $this->_public_url = SysCfg::$server_url.'/res/public';
		$this->_app_url = SysCfg::$server_url.'/'.HttpRequest::$_request['app_name'];
        
		$this->assign('index_url', $this->_index_url);		//当前模块的首页
		$this->assign('insert_url', $this->_index_url."/insert");		//当前模块的插入数据操作
		$this->assign('update_url', $this->_index_url."/update");		//当前模块的更新数据操作
		$this->assign('app_res_url', $this->_app_res_url);		//当前应用静态资源根目录
		//$this->assign('res_url', $this->_res_url);			//当前模块静态资源根目录
		$this->assign('skin_url', $this->_app_res_url.'/skin/'.Herosphp::getAppConfig('skin'));
		$this->assign('site_url', SysCfg::$server_url);     //网站根url
		$this->assign('app_url', $this->_app_url);          //当前应用的根url
		$this->assign('public_url', $this->_public_url);    //公共资源地址
		$this->assign('sys_config', SysCfg::$site_config);	//系统设置
		
	}
	
    /**
     * 添加操作
     * @param           array           $_data  数据模型 
     * @param           boolean         $_ajax  是否用ajax方式返回
     */
    public function myinsert( $_data, $_ajax = TRUE ) {
    	
        $_mdao = MD($this->_modelName);      //数据库操作dao类
        if ( $_mdao->insert($_data) ) {
            if ( $_ajax ) {
                AjaxResult::ajaxOk();
            } else {
                $this->setMessage('ok', '数据添加成功！');
                $this->location($this->_index_url);
            }
        } else {
            if ( $_ajax ) {
                AjaxResult::ajaxError();
            } else {
                $this->setMessage('error', '数据添加失败，请重试！');
                $this->location($this->getReferer());
            }
        }
		
    }
    
    /* 更新操作 */
    public function myupdate( $_data, $_ajax = TRUE ) {
    	
        $_mdao = MD($this->_modelName);
        $_id = intval($_REQUEST['id']);
        if ( $_id <= 0 ) die('Invaild args!');
        if ( $_mdao->update($_data, $_id) ) {
            if ( $_ajax ) {
                AjaxResult::ajaxOk();
            } else {
                $this->setMessage('ok', '数据更新成功！');
                $this->location($this->_index_url);
            }
        } else {
            if ( $_ajax ) {
                AjaxResult::ajaxError();
            } else {
                $this->setMessage('error', '数据更新失败，请重试！');
                $this->location($this->getReferer());
            }
        }
        
    }
    
    /* 删除操作 */
    public function del() {
    	
        $_id = intval($_GET['id']);
        if ( $_id <= 0 ) AjaxResult::ajaxResult('error', '必须传入操作记录的ID!');
        $_mdao = MD($this->_modelName);
        if ( $_mdao->delete($_id) ) AjaxResult::ajaxOk();
        else AjaxResult::ajaxError();
		
    }
    
    /* 批量删除操作 */
    public function multidel() {
    	
        $_act = $_GET['act'];
        if ( $_act == 'multidel' ) {
            $_ids = $_GET['ids'];
            $_mdao = MD($this->_modelName);
            if ( $_mdao->deletes("id in({$_ids})") ) AjaxResult::ajaxOk();
            else AjaxResult::ajaxError();
        } else {
            AjaxResult::ajaxResult('error', '非法参数！');
        }
		
    }
    
    /**
     * 设置查询字段
     * @param       string | array      $_fields 
     */
    protected function setFields( $_fields ) {
    	
        if ( !$_fields ) return;
        $this->_fields = $_fields;
		
    }
    
    /**
     * 设置排序
     * @param       string | array      $_orders  
     */
    protected function setOrders( $_orders ) {
    	
        if ( !$_orders ) return;
        $this->_order = $_orders;
    
	}
    
    /**
     * 设置查询条件
     * @param       string | array      $_conditions  
     */
    protected function setConditions( $_conditions ) {
    
	    if ( !$_conditions ) return;
        $this->_conditions = $_conditions;
    
	}
    
    /**
     * 设置每页显示记录数 
     * @param           int         $_pagesize
     */
    protected function setPagesize( $_pagesie ) {
    
	    if ( $_pagesie > 0 ) $this->_pagesize = $_pagesie;
    
	}
    
    /**
     * 设置页码数
     * @param       int         $_pageout 
     */
    protected function setPageout( $_pageout ) {
    
	    if ( $_pageout > 0 ) $this->_pageout = $_pageout;
    }
    
    /**
     * 设置操作提示消息 
     * @param       string          $_state     消息状态
     * @param       array           $_message   消息内容
     */
    protected function setMessage( $_state, $_message ) {
    
	    //将消息保存到session中
        $_SESSION['message'] = "{$_state}@{$_message}";
    }
	
}
?>