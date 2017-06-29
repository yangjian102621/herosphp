<?php
namespace app\demo\action;

use app\demo\dao\UserDao;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\db\utils\MysqlQueryBuilder;
use herosphp\http\HttpRequest;
use herosphp\model\MysqlModel;
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

        $model = Loader::model(UserDao::class);
        $model->fields('id,name')
            ->where('id', '>', 10)
            ->where('name', 'LIKE', '%xxxxx%')
            ->whereOr(function($model) use($model) {
                $model->where('name','yangjian')
                    ->where('address','IN',['dongguan','shenzheng','guangzhou']);
            })
            ->having(function($model) use ($model) {
                $model->having('name','xiaoming')
                    ->having('addtime', 'between',['2012-03-28','2012-04-29']);
            })
            ->group('name');
        echo $model->find();
        $item = $model->page(1,5)->find();
        __print($item);
        die();
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
