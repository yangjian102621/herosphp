<?php
namespace test\action;

use common\action\CommonAction;
use herosphp\bean\Beans;
use herosphp\core\Loader;
use herosphp\db\DBFactory;
use herosphp\db\entity\MongoEntity;
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

        //__print($db->delete(MongoEntity::getInstance()->setTable('user')->addWhere('_id', new \MongoId('57aadd587df95d37068b45d1'))));

        $model = Loader::model("news");
//        for ( $i = 0; $i < 100; $i++ ) {
//            $data = array('title' => 'title_'.$i, 'bcontent' => 'bcontent_'.$i);
//            $model->insert($data);
//        }
        //__print($model->getItems(MongoEntity::getInstance()->page(1)->pagesize(10)->order('_id DESC')));
        $query = MongoEntity::getInstance()
            ->addOrWhere('_id', new \MongoId('57ab392b7df95d5e048b49b7'))
            ->addOrWhere('title', 'title_92')
            ->addOrWhere('_id', new \MongoId('57ab392b7df95d5e048b49b8'));
        $items = $model->getItems($query);
        //$model->updates(array('hits' => 1), $query);
//        $model->increase('hits', 10, $query);
//        __print($model->getItems($query));

        foreach ($items as $value) {
            __print($value['_id']->__toString());
        }

        AjaxResult::ajaxSuccessResult();
    }
}
