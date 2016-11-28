<?php

/*---------------------------------------------------------------------
 * 应用程序生命周期匹配器
 * @package herosphp\listener
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\listener;

use herosphp\core\Loader;
use herosphp\http\HttpRequest;

Loader::import('listener.IWebAplicationListener', IMPORT_FRAME);

abstract class WebApplicationListenerMatcher implements  IWebAplicationListener {

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
     * 在控制器的住方法调用之后无论如何也会调用的，比如在控制器调用之后直接die掉，
     * 返回json视图，这样 beforeSendResponse()这个监听器是无法捕获的
     * @param HttpRequest $request
     * @param \herosphp\core\Controller $actionInstance
     * @return mixed
     */
    public function actionInvokeFinally(HttpRequest $request, $actionInstance) {
        // TODO: Implement beforeActionInvoke() method.
    }

    /**
     * 响应发送之前
     * @return mixed
     */
    public function beforeSendResponse(HttpRequest $request, $actionInstance)
    {
        // TODO: Implement beforeSendResponse() method.
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
