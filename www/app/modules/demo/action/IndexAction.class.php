<?php
namespace demo\action;

use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use qrcode\QRcode;

/**
 * 首页测试
 * @since           2015-01-28
 * @author          yangjian<yangjian102621@gmail.com>
 */
class IndexAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {
        $this->setView("index");
        $this->assign("title", "欢迎使用Herosphp");
    }

    /**
     * 显示一个二维码
     * @param HttpRequest $request
     */
    public function qrcode(HttpRequest $request) {
        QRcode::png("http://www.herosphp.com")->show();
    }

}
