<?php

// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// * Copyright 2014 The Herosphp Authors. All rights reserved.
// * Use of this source code is governed by a MIT-style license
// * that can be found in the LICENSE file.
// * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\annotation\RequestMap;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;

#[Controller(IndexAction::class)]
class UserController extends BaseController
{
    #[RequestMap(uri: '/admin/user/{username}/{id}', method: 'GET')]
    public function index(HttpRequest $request, $username, $id)
    {
        return var_dump_r($username, $id, $request);
        // return json_encode(['username' => $username, 'id' => $id,  'xxxx' => 1]);
    }

    #[Get(uri: ['/user/get', '/user/fetch'])]
    public function get(HttpRequest $request): HttpResponse
    {
        return $this->jsonView(0, ['hello' => 'world']);
    }
}
