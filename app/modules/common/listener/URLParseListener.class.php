<?php

namespace common\listener;

use herosphp\bean\Beans;
use herosphp\http\HttpRequest;
use herosphp\session\Session;
use herosphp\listener\IWebAplicationListener;
use herosphp\listener\WebApplicationListenerMatcher;
use herosphp\core\WebApplication;

/**
 * URL解析监听器
 * Class URLParseListener
 * @package common\listener
 * @author yangjian102621@gmail.com
 */
 class URLParseListener extends WebApplicationListenerMatcher implements IWebAplicationListener {

     private $httpRequest;

     /**
      * 请求拦截
      */
    public function beforeRequestInit() {


    }

    public function beforeActionInvoke(){

        
    }

}

?>
