<?php

namespace app\demo;

use herosphp\core\WebApplication;
use herosphp\http\HttpRequest;
use herosphp\listener\IWebAplicationListener;
use herosphp\listener\WebApplicationListenerMatcher;

/**
 * 当前模块请求的生命周期监听器
 * @author yangjian<yangjian102621@gmail.com>
 */
 class ModuleListener extends WebApplicationListenerMatcher implements IWebAplicationListener {

     /**
      * action 方法调用之前
      * @return mixed
      */
     public function beforeActionInvoke(HttpRequest $request)
     {
        //权限认证的代码可以写在这里
         //die("您没有权限。");
     }

     /**
      * 响应发送之前
      * @return mixed
      */
     public function beforeSendResponse(HttpRequest $request, $actionInstance)
     {

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
