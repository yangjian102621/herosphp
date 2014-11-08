<?php
/**
 * 缓存抽象类
 * @author      yangjian102621@gmail.com
 */
Abstract class ACache {

    protected static $_FILE_OPACITY = 1000;        /* 每个文件夹的文件容量 */

    protected $config = array();       /* 缓存配置参数 */

    /**
     * 获取缓存文件路径
     * @param       $_key       缓存key
     * @return      string
     */
    public function getCacheFile( $_key )
    {
        $_request = HttpRequest::getRequest();
        $_path = $this->config['cdir'].DIR_OS.$_request['module'].DIR_OS.$_request['action'];

        //通过hash映射到对应的缓存文件夹
        $_path .= DIR_OS.( bkdrHash($_key) % self::$_FILE_OPACITY );
        if ( !file_exists($_path) ) Utils::makeFileDirs($_path);
        $_filename = $_path .DIR_OS. md5($_key);

        return $_filename;
    }


}
