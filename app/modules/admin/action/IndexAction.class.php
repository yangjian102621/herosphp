<?php
namespace admin\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\exception\HeroException;
use herosphp\http\HttpRequest;

/**
 * 后台管理 index 控制器
 */
class IndexAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {

        $this->setView("index");
        $this->assign("indexPage", '/admin/index/form');

    }

    public function form(HttpRequest $request) {

        $this->setView("form");

    }
  
}
