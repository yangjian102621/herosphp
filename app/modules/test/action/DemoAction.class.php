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
use herosphp\string\StringUtils;
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

        $mongoModel = Loader::model('news');
        $mysqlModel = Loader::model('user');

//        $address = array('东莞','深圳','广州','北京','上海','杭州');
//        for ( $i = 0; $i < 100; $i++ ) {
//            $data = array(
//                "id" => StringUtils::genGlobalUid(true),
//                "name" => "user_{$i}",
//                "age" => $i,
//                "address" => $address[mt_rand(0,5)]);
//            __print($mongoModel->insert($data));
//            __print($mysqlModel->insert($data));
//        }

        //条件查询兼容mongodb,这样你可以随时更改模型而不需要更改任何业务代码
//        $conditions = array('age' => ">20", 'address' => '深圳');
//        $fields = array('name', 'age', 'address');
//        $limit = array(0,5);
//        $sort = array('age' => 1, 'name' => 1);
//        $items1 = $mongoModel->getItems($conditions, $fields, $sort, $limit);
//
//        $items2 = $mysqlModel->getItems($conditions, $fields, $sort, $limit);
//
//        __print($items1);
//        __print($items2);

        $item = $mongoModel->findOne();
        __print($item);

        $item['name'] = "超级无敌美少女 fuck";
        $item['age'] = 18;
        $item['address'] = "天朝上国";
        //var_dump($mongoModel->update($item, $item['_id']));
        //__print($mongoModel->findOne());

        var_dump($mysqlModel->update($item, $item['id']));
        __print($mysqlModel->getItem($item['id']));


        AjaxResult::ajaxSuccessResult();
    }
}
