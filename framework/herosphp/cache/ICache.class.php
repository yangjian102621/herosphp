<?php
/**
 * 缓存类统一接口，所有的缓存类必须实现这一接口。
 * cache operation class common interface.
 * 缓存的分类：对于文件缓存，其格式是这样的：type/app/module/action/{serial}/hash(key)/filename
 * 如：html/home/article/list/classid-100/hash(key)/article-list-100-{serial}.html
 * html/home/article/view/classid-100/hash(key)/article-view-100-{serial}.html
 * ---------------------------------------------
 * 版权所有 (C) 2013.03-now 网络星空工作室研发中心 并保留所有权利。 
 * ----------------------------------------------------------
 * @author 	yangjian<yangjian102621@gmail.com>
 * @completed	2013-04-11
 */
interface ICache {
	
	/**
	 * 获取缓存内容
	 * 
	 * @param	        string		         $_key	       缓存的key值。一般是sql语句或者是文件名
	 * @param	        string		         $_ctime	   缓存有效期
     * @return         mixed
	 */
	public function get( $_key, $_ctime );
	
	/**
	 * 添加|更新缓存
	 * @param   string               $_key         缓存的key值。一般是sql语句或者是文件名
     * @param   string               $_content     缓存内容
     * @param   boolean
	 */
	public function set( $_key, $_content);
	
	/**
	 * 删除缓存 
	 * @param   string               $_key         缓存的key值。一般是sql语句或者是文件名
     * @return   boolean
	 */
	public function delete( $_key);
		
}
?>