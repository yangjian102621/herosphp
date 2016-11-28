<?php

namespace common\listener;

use herosphp\core\WebApplication;
use herosphp\http\HttpRequest;
use herosphp\listener\IWebAplicationListener;
use herosphp\listener\WebApplicationListenerMatcher;

/**
 * 应用程序默认生命周期监听器
 * @package common\listener
 * @author yangjian102621@gmail.com
 */
 class DefaultWebappListener extends WebApplicationListenerMatcher implements IWebAplicationListener {

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

     }

     /**
      * 响应发送之前
      * @return mixed
      */
     public function beforeSendResponse(HttpRequest $request, $actionInstance)
     {
         $webApp = WebApplication::getInstance();
         //注册当前app的配置信息
         $actionInstance->assign('appConfigs', $webApp->getConfigs());
         $actionInstance->assign('params', $webApp->getHttpRequest()->getParameters());
     }

     /**
      * 响应发送之后
      * @return mixed
      */
     public function afterSendResponse($actionInstance)
     {
         // TODO: Implement afterSendResponse() method.
     }

}
