<?php
namespace test\action;

use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\filter\Filter;
use herosphp\http\HttpRequest;

Loader::import('filter.Filter', IMPORT_FRAME);

/**
 * 数据过滤测试
 * @since           2015-02-21
 * @author          yangjian<yangjian102621@gmail.com>
 */
class FilterAction extends Controller {

    /**
     * @param HttpRequest $request
     */
    public function index(HttpRequest $request) {

        $filterMap = array(
            'title' => array(DFILTER_STRING, array(6, 12), DFILTER_SANITIZE_TRIM, '标题'),
            'email' => array(DFILTER_EMAIL, NULL, NULL, '邮箱'),
            'id_number' => array(DFILTER_IDENTIRY, NULL, NULL, '身份证号码'),
            'content' => array(DFILTER_STRING, NULL, DFILTER_SANITIZE_HTML|DFILTER_MAGIC_QUOTES, '内容')
        );

        $data = array(
            'title' => 'xiaoyang333',
            'email' => '906388445@qq.com',
            'id_number' => '431028198801210839',
            'content' => "<span>我有一头'小毛驴'。</span>"
        );

        $data = Filter::loadFromModel($data, $filterMap, $error);
        __print($data);
        __print($error);

        die();

    }
  
}
?>
