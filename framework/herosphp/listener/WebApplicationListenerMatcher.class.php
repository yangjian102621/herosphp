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

use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

Loader::import('listener.IWebAplicationListener', IMPORT_FRAME);

abstract class WebApplicationListenerMatcher implements  IWebApplicationListener {

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
        // TODO: Implement beforeSendResponse() method.
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
