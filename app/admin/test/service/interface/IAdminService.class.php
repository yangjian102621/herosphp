<?php
/**
 * 管理员服务接口类
 * 
 * @author          yangjian<yangjian102621@163.com>
 * @since           2014.01.05 
 * @link            http://www.lionsoul.org
 */
interface IAdminService {
    
    /**
     * 当前登录的用户的的session key
     */
    const LOGINED_USER = 'logined_user';
    
    /**
     * 获取所有的管理员，并以id作为数组的key  
     * @return      array
     */
    public function getAllItems();
    
    /**
     * 用户登录 
     * @param           string      $_username      用户名
     * @param           string      $_password      密码
     * @param           string      $_safecode      验证码
     * @return          boolean
     */
    public function login( $_username, $_password, $_safecode );
    
    /**
     * 退出登录 
     * @param           string          $_url  要跳转的目的地址
     */
    public function loginout( $_url );
    
    
    /**
     * 判断当前用户是否登录 
     */
    public function isLogin();
    
    /**
     * 获取当前登录用户的信息
     * @return          array
     */
    public function getLoginUser();
}
?>