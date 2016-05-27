<?php
/*---------------------------------------------------------------------
 * 控制器抽象基类, 所有的控制器类都必须继承此类。
 * 每个操作对应一个方法。
 * ---------------------------------------------------------------------
 * Copyright (c) 2013-now http://blog518.com All rights reserved.
 * ---------------------------------------------------------------------
 * Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * ---------------------------------------------------------------------
 * Author: <yangjian102621@gmail.com>
 *-----------------------------------------------------------------------*/

namespace herosphp\core;

abstract class Controller extends Template {

    /**
     * 视图模板名称
     * @var string
     */
    private $view = null;

	/**
     * 控制器初始化方法，每次请求必须先调用的方法，action子类可以重写这个方法进行页面的初始化
	 */
	public function C_start() {

        $webApp = WebApplication::getInstance();
        //注册当前app的配置信息
        $this->assign('appConfigs', $webApp->getConfigs());
        $this->assign('params', $webApp->getHttpRequest()->getParameters());

    }

    /**
     * 设置视图模板
     * @param       string      $view      模板名称
     */
    public function setView( $view ) {
        $this->view = $view;
    }

    /**
     * 获取视图
     * @return string
     */
    public function getView() {
        return $this->view;
    }

}