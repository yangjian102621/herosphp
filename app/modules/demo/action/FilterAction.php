<?php
namespace app\demo\action;

use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\core\WebApplication;
use herosphp\filter\Filter;
use herosphp\http\HttpRequest;
use PHPExcel\Exception;

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
            'title' => array(Filter::DFILTER_STRING, array(6, 12), Filter::DFILTER_SANITIZE_TRIM,
                array("require" => "标题不能为空.", "length" => "标题长度必需在6-12之间.")),
            'email' => array(Filter::DFILTER_EMAIL, NULL, NULL,
                array("type" => "请输入正确的邮箱地址")),
            'mobile' => array(Filter::DFILTER_MOBILE, NULL, NULL,
                array("type" => "请输入正确的手机号码")),
            'id_number' => array(Filter::DFILTER_IDENTIRY, NULL, NULL,
                array('type' => '请输入正确的身份证号码')),
            'content' => array(Filter::DFILTER_STRING, NULL, Filter::DFILTER_MAGIC_QUOTES|Filter::DFILTER_SANITIZE_HTML,
                array("require" => "文章内容不能为空."))
        );

        $data = array(
            'title' => 'xiaoyang333',
            'email' => '906388445@qq.com',
            'mobile' => '185456701250',
            'id_number' => '431028198801210838',
            'content' => "<span>我有一头'小毛驴'。</span>"
        );

        $data = Filter::loadFromModel($data, $filterMap, $error);
        __print($data);
        __print($error);
        die();

    }
  
}
?>
