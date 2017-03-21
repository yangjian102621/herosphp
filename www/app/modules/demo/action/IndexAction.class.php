<?php
namespace demo\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\core\Debug;
use herosphp\core\WebApplication;
use herosphp\db\entity\MysqlEntity;
use herosphp\http\HttpRequest;
use herosphp\string\StringBuffer;
use herosphp\string\StringUtils;
use herosphp\utils\AjaxResult;
use herosphp\web\WebUtils;
use qrcode\QRcode;
use Workerman\Worker;

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
