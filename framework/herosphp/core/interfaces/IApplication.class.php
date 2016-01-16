<?php
/*---------------------------------------------------------------------
 * 应用程序接口类，定义应用程序的生命周期
 * @package herosphp\core\interfaces
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\core\interfaces;

interface IApplication {

    /**
     * 请求初始化
     * @return void
     */
    public function requestInit();

    /**
     * action当前访问的操作方法调用
     * @return void
     */
    public function actionInvoke();

    /**
     * 发送响应
     * @return void
     */
    public function sendResponse();
} 