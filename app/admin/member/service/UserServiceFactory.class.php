<?php
/**
 * user 模块工厂类 
 * 
 * @author          yangjian<yangjian102621@gmail.com>
 * @since           2014.01.05 
 * @link            http://www.lionsoul.org
 */
 
 class UserServiceFactory {
     
      private static $_admin_service = NULL;
     
     /**
      * 获取admin服务 
      */
     public static function getAdminService() {
     	
         if ( self::$_admin_service == NULL ) {
         
		     include __DIR__.DIRECTORY_SEPARATOR."interface".DIRECTORY_SEPARATOR."IAdminService.class.php";
             include __DIR__.DIRECTORY_SEPARATOR.'AdminService.class.php';
             self::$_admin_service = new AdminService();
         
		 }
         return self::$_admin_service;
     
	 }
     
 }
?>