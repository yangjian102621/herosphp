<?php
namespace test\action;

use herosphp\cache\CacheFactory;
use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;

/**
 * 缓存测试
 * @since           2013-12-28
 * @author          yangjian<yangjian102621@163.com>
 */
class CacheAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        $CACHER = CacheFactory::create('file');
        $CACHER->baseKey('article')->ftype('list')->factor(1);
        $item = $CACHER->get(null);
        if ( !$item ) {
            $model = Loader::model('article');
            $item = $model->getItems(null,null,"id desc",1,20);
            if ( $CACHER->set(null, $item) ) {
                __print("生成缓存成功！");
            }

        } else {
            __print($item);
        }

        die();

    }

    public function detail( HttpRequest $request ) {
        $CACHER = CacheFactory::create('file');
        $CACHER->baseKey('article')->ftype('detail')->factor('299');
        $item = $CACHER->get(null);
        if ( !$item ) {
            $model = Loader::model('article');
            $item = $model->getItem(299);
            $CACHER->set(null, $item);
        } else {
            __print($item);
        }

        die();
    }

    //静态缓存测试
    public function html( HttpRequest $request ) {
        $CACHER = CacheFactory::create('html');
        $CACHER->baseKey('article')->ftype('detail')->factor('299');
        $item = $CACHER->get(null);
        if ( !$item ) {
            $model = Loader::model('article');
            $item = $model->getItem(299);
            $this->assign('item', $item);
            $html = $this->getExecutedHtml();
            if ( $CACHER->set(null, $html) ) {
                __print("生成缓存成功！");
            }

        } else {
            echo $item;
        }
        die();
    }

    //memcache 测试
    public function memory( HttpRequest $request ) {

        $CACHER = CacheFactory::create('memo');
        $key = 'test.data';
        $data = $CACHER->get($key);
        if ( !$data ) {
            $CACHER->set($key, "测试 Memcache 缓存数据!");
        }
        __print($data);
        die();
    }
}
?>
