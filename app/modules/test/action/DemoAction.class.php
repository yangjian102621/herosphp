<?php
namespace test\action;

use common\action\CommonAction;
use herosphp\bean\Beans;
use herosphp\core\Loader;
use herosphp\db\DBFactory;
use herosphp\db\entity\MongoEntity;
use herosphp\db\entity\MysqlEntity;
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

    //测试联合查询
    public function join() {

        $service = Beans::get('test.user.service');
        //$list = $service->getDB()->getList("select a.id, a.username, a.password, b.title, b.bcontent from fiidee_user a, fiidee_news b where a.id=b.userid");

//        $query = MysqlEntity::getInstance()->table("{prefix}user a, {prefix}article b")->field('a.id, a.username, a.password, b.title, b.bcontent')->where('a.id=b.userid');
//        $list = $service->getItems($query);

        $query = MysqlEntity::getInstance()
            ->leftJoin("{prefix}news a")
            ->on("{t}.id = a.id")
            ->field("{t}.id, a.title, a.bcontent, {t}.username,{t}.password")
            ->where("1=1");
        $list = $service->getItems($query);
        __print($list);

        die();
    }

    public function mongo() {
        $congfig = Loader::config('db');
        $db = DBFactory::createDB('mongo', $congfig['mongo']);
//        for ( $i = 0; $i < 100; $i++ ) {
//            $data = array(
//                'username' => 'xiaoming_'.$i,
//                'password' => md5(time()),
//                'addtime' => time()
//            );
//            $entity = MongoEntity::getInstance()->setData($data)->setTable('user');
//            $db->insert($entity);
//        }

//        $entity = MongoEntity::getInstance()->setTable('user')->where(array('username' => 'xiaoming_0'));
//        $obj = $db->getOneRow($entity);
//        __print($obj);
//        $obj['address2'] = '东莞3';
//        $entity->setData($obj);
//        var_dump($db->replace($entity));
//
//        $obj = $db->getOneRow($entity);
//        __print($obj);

//        $entity = MongoEntity::getInstance()->setTable('user')->field('username, password')->where(array('username' => 'xiaoming_0'));
//        __print($db->getOneRow($entity));

//        $entity = MongoEntity::getInstance()->setTable('user')->field('username, password')->page(1)->pagesize(5);
//        __print($db->getList($entity));

        //__print($db->count(MongoEntity::getInstance()->setTable('user')));

//        __print($db->getList(MongoEntity::getInstance()->setTable('user')->addWhere('username', 'xiaoming_2')));
//        $data = array('username' => '张三', 'address' => '广州');
//        $entity = MongoEntity::getInstance()->setTable('user')->addWhere('username', 'xiaoming_2')->setData($data);
//        __print($db->update($entity));
//        __print($db->getList(MongoEntity::getInstance()->setTable('user')->addWhere('username', '张三')));

        __print($db->delete(MongoEntity::getInstance()->setTable('user')->addWhere('_id', new \MongoId('57aadd587df95d37068b45d1'))));

        AjaxResult::ajaxSuccessResult();
    }
}
