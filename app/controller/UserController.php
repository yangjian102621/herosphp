<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\annotation\Post;
use herosphp\annotation\RequestMap;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;

#[Controller(IndexAction::class)]
class UserController extends BaseController
{
    #[RequestMap(uri: ['/', '/index'], method: 'GET')]
    public function index(HttpRequest $request): HttpResponse
    {
        return $this->view('index', ['title' => 'Hello, world!']);
    }

    #[RequestMap(uri: '/user/{username}', method: 'GET')]
    public function var(HttpRequest $request, $username)
    {
        return var_dump_r($username, $request);
    }

    #[Get(uri: ['/news/get', '/news/fetch'])]
    public function get(HttpRequest $request): HttpResponse
    {
        return $this->jsonView(0, ['hello' => 'world', 'var' => $request->get('var')]);
    }

    #[Post(uri: '/user/add')]
    public function post(): string
    {
        return 'Not implemented yet.';
    }
}
