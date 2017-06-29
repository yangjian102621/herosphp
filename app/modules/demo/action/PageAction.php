<?php
namespace app\demo\action;

use herosphp\core\Controller;
use herosphp\core\Loader;
use herosphp\http\HttpRequest;
use herosphp\session\FileSession;
use herosphp\files\FileUtils;
use herosphp\utils\Page;

/**
 * 分页测试
 * @author          yangjian<yangjian102621@gmail.com>
 */
class PageAction extends Controller {

    /**
     * 首页方法
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        $page = $request->getParameter('page', 'intval');
        $pagesize = 10;

        if ( $page <= 0 ) $page = 1;

        $total = 1000;

        //初始化分页类
        $pageHandler = new Page($total, $pagesize, $page);

        //获取分页数据
        $pageData = $pageHandler->getPageData(DEFAULT_PAGE_STYLE);
        //组合分页HTML代码
        if ( $pageData ) {
            $pagemenu = '<ul class="pagination">';
            $pagemenu .= '<li><a href="'.$pageData['prev'].'" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a></li>';
            foreach ( $pageData['list'] as $key => $value ) {
                if ( $key == $page ) {
                    $pagemenu .= '<li class="active"><a href="#fakelink">'.$key.'</a></li> ';
                } else {
                    $pagemenu .= '<li><a href="'.$value.'">'.$key.'</a></li> ';
                }
            }
            $pagemenu .= '<li><a href="'.$pageData['next'].'" aria-label="Next"><span aria-hidden="true">&raquo;</span></a></li>';
            $pagemenu .= '</ul>';
        }

        $this->assign('pagemenu', $pagemenu);

        //设置视图
        $this->setView('article_page');

    }
  
}