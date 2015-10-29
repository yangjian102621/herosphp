<?php

/*---------------------------------------------------------------------
 * 应用程序生命周期匹配器
 * @package herosphp\listener
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@163.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\listener;

use herosphp\core\Loader;

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
    public function beforeActionInvoke()
    {
        // TODO: Implement beforeActionInvoke() method.
    }

    /**
     * 响应发送之前
     * @return mixed
     */
    public function beforeSendResponse($actionInstance)
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
