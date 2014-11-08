<?php
/**
 * 管理员用户
 * 
 * @since           2013-12-28
 * @author          yangjian<yangjian102621@gmail.com>
 */
class LoginAction extends Action {
    
    /**
     * 登录界面 
     */
    public function index() {
    	if ( !isset($_SESSION) ) session_start();
		//如果已经登录了则直接返回用户中心
		if ( $_SESSION['admin_id'] > 0 )
			header("location:{$this->_app_url}/common/ucenter/index.html");
		
        $this->assign('title', '塑搜系统管理-用户登录');
		
    }
	
	/**
	 * 登录验证操作 
	 */
	public function signin() {
		
		//登录验证
        $_act = trim($_POST['act']);
        if ( $_act == 'login_check' ) {
        	
            $_username = trim($_POST['username']);    
            $_password = trim($_POST['password']);
            $_safecode = trim($_POST['safecode']);    
            Herosphp::import('member.service.UserServiceFactory');
            $_admin_service = UserServiceFactory::getAdminService();
            $_login = $_admin_service->login($_username, $_password, $_safecode);
			
            switch ( $_login ) {
                case 0:
                    AjaxResult::ajaxResult('error', '验证码错误！');
                    break;
                case 1:
                    AjaxResult::ajaxResult('ok', '登录成功！');
                    break;
                case -1:
                    AjaxResult::ajaxResult('error', '用户名或者密码错误！');
                    break;
                default:
                    AjaxResult::ajaxResult('error', '登录失败！');
            }
        }
		
	}
    
    /**
     * 退出登录 
     */
    public function logout() {
    	
        Herosphp::import('member.service.UserServiceFactory');
        $_admin_service = UserServiceFactory::getAdminService();
        $_admin_service->loginout( $this->_app_url."/member/login/index.html" );
		
    }
  
}
?>
