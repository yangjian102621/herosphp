<?php
namespace {module}\action;

use admin\action\CommonAction;
use herosphp\http\HttpRequest;

/**
 * {desc}
 * @author {author}
 * @email {email}
 * @date {date}
 */
class {className} extends CommonAction {

    {serviceBean}

    //数据列表页面
    public function index( HttpRequest $request ) {
       //some logic code here
    }

    //数据添加页面
    public function add(HttpRequest $request) {
        $this->setView('');
    }

    //数据编辑页面
    public function edit(HttpRequest $request) {
        parent::edit($request);
        $this->setView('');
    }

    //插入数据操作
    public function insert(HttpRequest $request) {

        $data = $request->getParameter('data');
        parent::__insert($data);
    }

    //更新数据操作
    public function update(HttpRequest $request) {

        $data = $request->getParameter('data');
        $id = $request->getParameter('id', 'trim');
        parent::__update($data, $id);

    }
}
