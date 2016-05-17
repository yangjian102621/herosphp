<?php
namespace common\action;

use herosphp\bean\Beans;
use herosphp\core\Controller;
use herosphp\http\HttpRequest;
use herosphp\utils\Page;

define('COM_ERR_MSG', '(⊙o⊙) 系统出了小差！');

/**
 * 模块通用 Action
 * @author          yangjian<yangjian102621@gmail.com>
 */
abstract class CommonAction extends Controller {

    /**
     * 当前页面
     * @var int
     */
    protected $page = 1;

    /**
     * 没页显示多少条记录
     * @var int
     */
    protected $pagesize = 15;

    /**
     * 查询条件
     * @var string|array
     */
    protected $conditions;

    /**
     * 排序方式
     * @var string|array
     */
    protected $order;

    /**
     * 分组字段
     * @var string
     */
    protected $group;

    /**
     * 分组条件
     * @var string|array
     */
    protected $having;

    /**
     * 查询字段
     * @var string|array
     */
    protected $fields;

    /**
     * 管理员用户
     * @var array
     */
    protected $loginUser;

    /**
     * Beans服务的key
     * @var string
     */
    protected $serviceBean;

    /**
     * 首页列表
     * @param HttpRequest $request
     */
    public function index( HttpRequest $request ) {

        $this->page = $request->getParameter('page', 'intval');
        if ( $this->page <=0 ) {
            $this->page = 1;
        }
        $service = Beans::get($this->getServiceBean());
        $total = $service->count($this->getConditions());
        $items = $service->getItems($this->getConditions(), $this->getFields(), $this->getOrder(),
            $this->getPage(), $this->getPagesize(), $this->getGroup(), $this->getHaving());
        //初始化分页类
        $pageHandler = new Page($total, $this->getPagesize(), $this->getPage(), 4);

        //获取分页数据
        $pageData = $pageHandler->getPageData(DEFAULT_PAGE_STYLE);
        //组合分页HTML代码
        if ( $pageData ) {
            $pagemenu = '<ul class="pagination blog-pagination">';
            $pagemenu .= '<li><a href="'.$pageData['prev'].'">PREV</a></li> ';
            foreach ( $pageData['list'] as $key => $value ) {
                if ( $key == $this->page ) {
                    $pagemenu .= '<li class="active"><a href="#fakelink">'.$key.'</a></li> ';
                } else {
                    $pagemenu .= '<li><a href="'.$value.'">'.$key.'</a></li> ';
                }
            }
            $pagemenu .= '<li><a href="'.$pageData['next'].'">NEXT</a></li> ';
            $pagemenu .= '</ul>';
        }

        $this->assign('pagemenu', $pagemenu);
        $this->assign('items', $items);
    }

    /**
     * 编辑操作
     * @param HttpRequest $request
     * @return void
     */
    public function edit(HttpRequest $request) {

        $id = $request->getParameter('id', 'intval');
        if ( $id <= 0 ) {
            $this->showMessage('danger', COM_ERR_MSG);
        } else {

            $service = Beans::get($this->getServiceBean());
            $item = $service->getItem($id);
            $this->assign('item', $item);

        }
    }

    /**
     * 插入数据
     * @param array $data
     */
    public function insert( $data ) {

        $service = Beans::get($this->getServiceBean());

        if ( $service->add($data) ) {
            AjaxResult::ajaxSuccessResult();
        } else {
            AjaxResult::ajaxFailtureResult();
        }
    }

    /**
     * 更新数据
     * @param array $data
     * @param HttpRequest $request
     */
    public function update( $data, HttpRequest $request ) {

        if ( !$data ) AjaxResult::ajaxFailtureResult();

        $id = $request->getParameter('id', 'intval');
        if ( $id <= 0 ) AjaxResult::ajaxResult('error', COM_ERR_MSG);

        $service = Beans::get($this->getServiceBean());
        if ( $service->update($data, $id) ) {
            AjaxResult::ajaxSuccessResult();
        } else {
            AjaxResult::ajaxFailtureResult();
        }

    }

    /**
     * 快速保存
     * @param HttpRequest $request
     */
    public function quicksave( HttpRequest $request ) {

        $hids = $request->getParameter('hids');
        $datas = $request->getParameter('data');
        $service = Beans::get($this->getServiceBean());
        $counter = 0;
        // 保存数据
        foreach ( $hids as $key => $id ) {
            if ( $service->update($datas[$key], $id) ) {
                $counter++;
            }
        }

        //全部数据保存成功，则该操作成功
        if ( $counter == count($hids) ) {
            AjaxResult::ajaxResult('ok', '保存成功！');
        } else {
            AjaxResult::ajaxResult('error', '保存失败！');
        }
    }

    /**
     * 删除单条数据
     * @param HttpRequest $request
     */
    public function delete( HttpRequest $request ) {

        $id = $request->getParameter('id', 'intval');
        if ( $id <= 0 ) AjaxResult::ajaxResult('error', COM_ERR_MSG);
        $service = Beans::get($this->getServiceBean());
        if ( $service->delete($id) ) {
            AjaxResult::ajaxSuccessResult();
        } else {
            AjaxResult::ajaxFailtureResult();
        }
    }

    /**
     * 删除多条数据
     * @param HttpRequest $request
     */
    public function deletes( HttpRequest $request ) {

        $ids = $request->getParameter('ids');
        if ( empty($ids) ) AjaxResult::ajaxResult('error', COM_ERR_MSG);
        $service = Beans::get($this->getServiceBean());
        if ( $service->deletes($ids) ) {
            AjaxResult::ajaxSuccessResult();
        } else {
            AjaxResult::ajaxFailtureResult();
        }
    }

    /**
     * 检验某个字段的值是否在数据库中存在，用于保持某个字段的唯一性
     * @param string $field 字段值
     * @param string $value 字段名
     */
    protected function checkField($field, $value) {

        $value = trim($value);
        $service = Beans::get($this->getServiceBean());
        $exists = $service->getItem(array($field => $value));
        if ( $exists ) {
            AjaxResult::ajaxResult('error', "{$value} 在数据库中已存在，请更换！");
        }

    }

    /**
     * 信息显示模板
     * @param $type 消息类型 info warnning success danger
     * @param $message
     * @param $url
     */
    public function showMessage( $type, $message, $url ) {

        $url = url("/common_message_index/type-{$type}-message-".urlencode($message)."-url-".urlencode($url));
        $this->location($url);

    }

    /**
     * @param HttpRequest $request
     */
    public function message( HttpRequest $request ) {

        $this->assign('type', $request->getParameter('type'));
        $this->assign('message', $request->getParameter('message', 'urldecode'));
        $this->assign('url', $request->getParameter('url', 'urldecode'));
        $this->setView('message');

    }

    /**
     * @param array|string $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = $conditions;
    }

    /**
     * @return array|string
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param array|string $fields
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array|string
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param string $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param array|string $having
     */
    public function setHaving($having)
    {
        $this->having = $having;
    }

    /**
     * @return array|string
     */
    public function getHaving()
    {
        return $this->having;
    }

    /**
     * @param array|string $order
     */
    public function setOrder($order)
    {
        $this->order = $order;
    }

    /**
     * @return array|string
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * @param int $page
     */
    public function setPage($page)
    {
        $this->page = $page;
    }

    /**
     * @return int
     */
    public function getPage()
    {
        return $this->page;
    }

    /**
     * @param int $pagesize
     */
    public function setPagesize($pagesize)
    {
        $this->pagesize = $pagesize;
    }

    /**
     * @return int
     */
    public function getPagesize()
    {
        return $this->pagesize;
    }

    /**
     * @param string $serviceBean
     */
    public function setServiceBean($serviceBean)
    {
        $this->serviceBean = $serviceBean;
    }

    /**
     * @return string
     */
    public function getServiceBean()
    {
        return $this->serviceBean;
    }

}