<?php

namespace common\listener;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use herosphp\listener\IWebApplicationListener;
use herosphp\listener\WebApplicationListenerMatcher;

/**
 * 应用程序默认生命周期监听器
 * @package common\listener
 * @author yangjian102621@gmail.com
 */
 class DefaultWebappListener extends WebApplicationListenerMatcher implements IWebApplicationListener {

     /**
      * 请求初始化之前
      * @return mixed
      */
     public function beforeRequestInit()
     {
         // TODO: Implement beforeRequestInit() method.
     }

     /**
      * action 方法调用之前
      * @return mixed
      */
     public function beforeActionInvoke(HttpRequest $request)
     {
         // TODO: Implement beforeActionInvoke() method.
     }

     /**
      * 响应发送之前
      * @return mixed
      */
     public function beforeSendResponse(HttpRequest $request, Controller $actionInstance)
     {
     }

     /**
      * 响应发送之后
      * @return mixed
      */
     public function afterSendResponse(Controller $actionInstance)
     {
         // TODO: Implement afterSendResponse() method.
     }

}
