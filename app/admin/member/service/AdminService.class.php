<?php
/**
 * 管理员服务类
 * 
 * @author          yangjian<yangjian102621@gmail.com>
 * @since           2014.01.05 
 * @link            http://www.lionsoul.org
 */
 
class AdminService implements IAdminService {
    
	public function __construct() {
		
		if(!isset($_SESSION)) session_start();
		
	}
	
    /**
     * @see         IAdminService::getAllItem() 
     */
    public function getAllItems() {
    	
        $_md_admin = MD('admin');
        $_admins = $_md_admin->getList();
        return changeArrayKey($_admins);
		
    }
    
    /**
      * @see         IAdminService::login() 
      * @return      int 
      */
     public function login( $_username, $_password, $_safecode ) {
     	 
		 if(!isset($_SESSION)) session_start();
         $_safecode = strtoupper($_safecode);
         if ( $_safecode != $_SESSION['scode'] ) return 0;
         $_password = md5($_password);
         $_md_user = MD('admin');
         $_res = $_md_user->getOneRow("username='{$_username}' AND password='{$_password}'");
         if ( $_res ) {
             $_SESSION[self::LOGINED_USER] = $_res;
             $_SESSION['admin_id'] = $_res['id'];
             return 1;
         } else {
             return -1;
         }
		 
     }
     
     /**
      * @see        IAdminService::loginout() 
      * @param      $_url       
      */
     public function loginout( $_url ) {
     	 
		 $_SESSION[self::LOGINED_USER] = NULL;
         session_destroy();
         header("location:{$_url}");
		 
     }
     
     /**
      * @see        IAdminService::isLogin() 
      */
     public function isLogin() {
     	
         return isset($_SESSION[self::LOGINED_USER]);
		 
     }
     
     /**
      * @see         IAdminService::getLoginUser()
      */
     public function getLoginUser() {
     	
         return $_SESSION[self::LOGINED_USER];
		 
     }
}
?>