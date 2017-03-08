<?php
namespace demo\action;

use common\action\CommonAction;
use herosphp\bean\Beans;
use herosphp\core\Loader;
use herosphp\db\mysql\MysqlQueryBuilder;
use herosphp\http\HttpRequest;
use herosphp\string\StringUtils;
use herosphp\utils\AjaxResult;
use herosphp\utils\FileUpload;
use herosphp\utils\HashUtils;

/**
 * demo action
 * @package commom\action
 * @author yangjian<yangjian102621@gmail.com>
 */
class DbAction extends CommonAction {

    public function index(HttpRequest $request) {

        __print($request->getParameters());

//        $service = Beans::get("test.user.service");
//        __print($service);
        $this->setView("index");
        $this->assign("title", "欢迎使用Herosphp");

        $conditions = array(
            'name' => 'xiaoming',
            '$or' => array('name' => 'xiaoming', 'address' => 'shenzhen'));
        $builder = MysqlQueryBuilder::buildConditions($conditions);
        die($builder);
    }

    //mysql模型测试
    public function mysql(HttpRequest $request) {

        $model = Loader::model("userInfo");

        //添加数据 C_Model::insert();
//        $address = array('东莞','深圳','广州','北京','上海','杭州');
//        for ( $i = 0; $i < 100; $i++ ) {
//            $model->setShardingRouter($i+1);
//            $data = array(
//                "name" => "user_{$i}",
//                "age" => $i,
//                "mobile" => "1857567012{$i}",
//                "bcontent" => "bcontent_".($i-1),
//                "email" => "908799776{$i}@qq.com",
//                "shop_name" => "小明的AV种子店{$i}",
//                "shop_address" => "大东莞厚街",
//                "shop_type" => "成人用品",
//                "address" => $address[mt_rand(0,5)]);
//
//            var_dump($model->add($data));
//        }
//        $list = $model->limit(8, 5)->field('userid,email')->sort(array("mobile" => 1, 'bcontent' => -1))->find();
//        __print($list);
//        $list_1 = $model->limit(1, 40)->field('userid,email')->sort(array("mobile" => 1, 'bcontent' => -1))->find();
//        __print($list_1);

//        $data = array(
//            'username' => "xiaoming",
//            'password' => "password_xiaoming",
//            'mobile' => "1876575468",
//        );
//        var_dump($model->add($data));
        $id = 'b21a57edb51c058cd854d2d420f98a5e';
        __print($model->getItem($id));
        $data = array(
            'username' => "xiaoming_update",
            'password' => "password_xiaoming_update",
            'mobile' => "1876575468_update",
        );
        $model->update($data, $id);

        __print($model->getItem($id));

//        for ($i = 0; $i < 100; $i++) {
//            $model->setShardingRouter($i+1);
//            $data = array(
//                'username' => "username_{$i}",
//                'password' => "password_{$i}",
//                'mobile' => "password_{$i}",
//            );
//            var_dump($model->add($data));
//        }
//        __print($model->getItem('b21a57ed1edd03ee3060379480cd4454'));
//        __print($model->count());
//        $conditions = array('userid' => array('$in' => array('b21a57eb8ba802b408281595e0711199', 'b21a57eb8bec00ac2970ab88b88dc4e5')));
//        __print($model->getItems($conditions));
//        $data = array(
//        "mobile" => "18575670121",
//        "bcontent" => "bcontent_update",
//        "email" => "908799776_update@qq.com");
//        var_dump($model->deletes($conditions));
//
//        __print($model->getItems($conditions));

//        $data = array(
//                "name" => "user_update",
//                "age" => 123,
//                "mobile" => "18575670125",
//                "bcontent" => "bcontent_update",
//                "email" => "908799776_update@qq.com",
//                "mobile" => "18575670125",
//                "shop_name" => "小明的AV种子店_update",
//                "shop_address" => "大东莞厚街_update",
//                "shop_type" => "成人用品_update",
//                "address" => '_update');
//
//        $conditions = array('name' => 'user_5');
//        var_dump($model->updates($data, $conditions));

//        $conditions = array('name' => 'user_update');
//        $items = $model->getItems($conditions);
//        __print($items);
//        $conditions = array('title' => '%abc%', 'name' => 'asdasdasd');
//        die(MysqlQueryBuilder::buildConditions($conditions));

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
//            'age' => array('>' => 20, '<' => 30),
//            '$or' => array('name' => 'xiaoming', 'age' => '>30'),
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

//        $model->beginTransaction();
//        $condition = array('id' => array('$in' => array('B21A-57B30872-01655B98-538F-FBED5277', 'B21A-57B30872-0125BDA8-8184-2B481B89')));
//        $model->sets('age', 500, $condition);
//        __print($model->getItems($condition));
//        $model->rollback();
//        __print($model->getItems($condition));



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

//        $conditions = array('name' => array('$like' => 'user_1'));
//        __print($model->getItems($conditions));


        //分组查询
        $keys = array("address" => 1);
        $initial = array("items" => array());
        $reduce = "function (obj, prev) { prev.items.push({id:obj.id, name:obj.name, age:obj.age}); }";
        $conditions = array('age' => '>30');
        $items = $model->findByGroup($keys, $initial, $reduce, $conditions);
        __print($items);


        AjaxResult::ajaxSuccessResult();
    }

    public function service() {

        $service = Beans::get('test.user.service');
        __print($service->getItem(123));
        AjaxResult::ajaxSuccessResult();
    }
}
