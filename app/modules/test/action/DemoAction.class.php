<?php
namespace test\action;

use common\action\CommonAction;
use herosphp\bean\Beans;
use herosphp\core\Loader;
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

        $model = Loader::model("user");
        //添加数据 C_Model::insert();
//        $address = array('东莞','深圳','广州','北京','上海','杭州');
//        for ( $i = 0; $i < 100; $i++ ) {
//            $data = array(
//                "id" => StringUtils::genGlobalUid(true),
//                "name" => "user_{$i}",
//                "age" => $i,
//                "address" => $address[mt_rand(0,5)]);
//
//            var_dump($model->insert($data));
//        }

        //C_Model::query();
//        $list = $model->query("select * from fiidee_user order by id desc limit 20");
//        __print($list);

        //C_Model:replace();
//        $data = array(
//            'id' => 'B21A-57B30877-0027B08C-748A-84D4EB10',
//            'name' => 'xiaoyang',
//            'address' => '南城高盛');
//        var_dump($model->replace($data));

        //C_Model::getItem()
//        $condition = array('id' => 'B21A-57B30877-0027B08C-748A-84D4EB10');
//        $one = $model->getItem($condition);
//        __print($one);

        //C_Model::delete()
//        $id = 'B21A-57B30877-0027B08C-748A-84D4EB10';
//        $model->delete($id);

        //C_Model::deletes(), C_Model::find
//        $conditions = array(
//            'name' => 'user_1',
//            '$or' => array('name' => 'xiaoming', 'age' => '>20'),
//            'address' => '深圳'
//        );
//
//        $gets = array('id', 'name', 'address');
//        $sort = array('id' => -1, 'name' => 1);
//        $list = $model->where($conditions)
//            ->field($gets)
//            ->sort($sort)
//            ->limit(0, 20)
//            ->group('address')
//            ->having(array('address' => array('$in' => array('深圳', '广州', '北京'))))
//            ->find();

        //C_Model::update
//        $data = array(
//            'name' => 'xiaoming',
//            'age' => 30,
//            'address' => '我爱北京天安门'
//        );
//        $conditions = array('id' => 'B21A-57B30872-01655B98-538F-FBED5277');
//        $model->updates($data, $conditions);
//        __print($model->where($conditions)->findOne());

        //C_Model::count
//        $conditions = array('id' => '>B21A-57B30872-01655B98-538F-FBED5277');
//        var_dump($model->count($conditions));

        //C_Model::increase
//        $condition = array('id' => array('$in' => array('B21A-57B30872-01655B98-538F-FBED5277', 'B21A-57B30872-0125BDA8-8184-2B481B89')));
//        //$model->batchIncrease('age', 10, $condition);
//        $model->batchReduce('age', 10, $condition);
//        __print($model->getItems($condition));

        //C_Model::set
//        $condition = array('id' => array('$in' => array('B21A-57B30872-01655B98-538F-FBED5277', 'B21A-57B30872-0125BDA8-8184-2B481B89')));
//        $model->sets('age', 200, $condition);
//        __print($model->getItems($condition));

        $model->beginTransaction();
        $condition = array('id' => array('$in' => array('B21A-57B30872-01655B98-538F-FBED5277', 'B21A-57B30872-0125BDA8-8184-2B481B89')));
        $model->sets('age', 500, $condition);
        __print($model->getItems($condition));
        $model->rollback();
        __print($model->getItems($condition));



        die('fuck it whatever.');
    }

    public function mongo() {

        $model = Loader::model("news");
        //添加数据 C_Model::insert();
//        $address = array('东莞','深圳','广州','北京','上海','杭州');
//        for ( $i = 0; $i < 100; $i++ ) {
//            $data = array(
//                "id" => StringUtils::genGlobalUid(true),
//                "name" => "user_{$i}",
//                "age" => $i,
//                "address" => $address[mt_rand(0,5)]);
//
//            __print($model->insert($data));
//        }

        //C_Model::query();
//        $list = $model->query("select * from fiidee_user order by id desc limit 20");
//        __print($list);

        $id = 'B21A-57B319E4-0269D8C0-D5CB-8295ECE2';
        //C_Model::getItem()
//        $one = $model->getItem($id);
//        __print($one);

        //C_Model::delete()
//        $model->delete($id);
//        __print($model->getItem($id));

        //C_Model::deletes()
//        $conditions = array(
//            'name' => 'user_1',
//            '$or' => array('id' => array('$in' => array('B21A-57B319E4-026B4D68-F5E5-9DF9281A', 'B21A-57B319E4-026C46A0-248C-1907B7D3')))
//        );
//        var_dump($model->deletes($conditions));

        //C_Model::find
//        $gets = array('id', 'name', 'address');
//        $sort = array('name' => 1);
//        $list = $model->where()
//            ->field($gets)
//            ->sort($sort)
//            ->limit(0, 20)
//            ->find();
//        __print($list);

        //C_Model::update
//        $data = array(
//            'name' => 'xiaoming',
//            'age' => 30,
//            'address' => '我爱北京天安门'
//        );
//        $conditions = array('id' => 'B21A-57B319E4-026D4230-E18D-817D454C');
//        $model->updates($data, $conditions);
//        __print($model->where($conditions)->findOne());

        //C_Model::count
//        $conditions = array('id' => '>B21A-57B30872-01655B98-538F-FBED5277');
//        var_dump($model->count($conditions));

        //C_Model::increase
//        $condition = array('id' => array('$in' => array('B21A-57B319E4-026DA5CC-79FC-091DB526', 'B21A-57B319E4-026BC748-7837-42B51E30')));
//        $model->batchIncrease('age', 10, $condition);
//        //$model->batchReduce('age', 10, $condition);
//        __print($model->getItems($condition));

        //C_Model::set
//        $condition = array('id' => array('$in' => array('B21A-57B319E4-026DA5CC-79FC-091DB526', 'B21A-57B319E4-026BC748-7837-42B51E30')));
//        $model->sets('age', 200, $condition);
//        $model->sets('name', "超级无敌美少女", $condition);
//        __print($model->getItems($condition));

        $conditions = array('name' => array('$like' => 'user_1'));
        __print($model->getItems($conditions));


        AjaxResult::ajaxSuccessResult();
    }

    public function service() {

        $service = Beans::get('test.user.service');
        __print($service->getItems());
        AjaxResult::ajaxSuccessResult();
    }
}
