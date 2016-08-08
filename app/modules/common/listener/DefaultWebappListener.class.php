<?php

namespace common\listener;

use herosphp\bean\Beans;
use herosphp\http\HttpRequest;
use herosphp\session\Session;
use herosphp\listener\IWebAplicationListener;
use herosphp\listener\WebApplicationListenerMatcher;
use herosphp\core\WebApplication;

/**
 * 应用程序默认生命周期监听器
 * @package common\listener
 * @author yangjian102621@gmail.com
 */
 class DefaultWebappListener extends WebApplicationListenerMatcher implements IWebAplicationListener {

    public function beforeRequestInit() {}

    public function beforeActionInvoke(){}

}
