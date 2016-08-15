<?php
namespace test\action;

use common\action\CommonAction;
use herosphp\bean\Beans;
use herosphp\core\Loader;
use herosphp\db\DBFactory;
use herosphp\db\entity\MongoEntity;
use herosphp\db\mongo\MongoQueryBuilder;
use herosphp\db\mysql\MysqlQueryBuilder;
use herosphp\http\HttpRequest;
use herosphp\utils\AjaxResult;
use herosphp\utils\FileUpload;

/**
 * demo action
 * @package commom\action
 * @author yangjian<yangjian102621@gmail.com>
 */
class DemoAction extends CommonAction {

    public function index(HttpRequest $request) {
        $this->setView("index");
        $this->assign("title", "欢迎使用Herosphp");
    }

    //mysql模型测试
    public function mysql() {

        $condition = array(
            'name' => 'xiaoming',
            'age' => array('>' => 18, '<=' => 30),
            '$or' => array('age' => array('>' => 19), 'name' => '小王'),
            'addtime' => array('>' => date('Y-m-d H:i:s')),
            'title' => array('like' => '%测试文章%'),
            '|username' => array('in' => array('xiaoyang', 'xiaoming', 'xiaoliu'))
        );

        $fields = array('id', 'username', 'password');

        $query = MysqlQueryBuilder::getInstance()
            ->table("user")
            ->fields($fields)
            ->where($condition)
            ->order(array('id' => 1, 'username' => -1))
            ->limit(10)
            ->buildQueryString();
        __print($query);

        die('fuck it whatever.');
    }

    public function mongo() {

        $model = Loader::model('news');

//        $address = array('东莞','深圳','广州','北京','上海','杭州');
//        for ( $i = 0; $i < 100; $i++ ) {
//            $data = array("name" => "user_{$i}", "age" => $i, "address" => $address[mt_rand(0,5)]);
//            $model->insert($data);
//        }

        $items = $model->where(array('age' => array('>' => 10)))
                       ->field('name, age, address')
                       ->limit(10, 5)
                       ->sort("age DESC")
                       ->find();

        __print($items);

        AjaxResult::ajaxSuccessResult();
    }
}
