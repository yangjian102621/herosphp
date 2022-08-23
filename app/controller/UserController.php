<?php

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\RequestMap;
use herosphp\core\HttpRequest;

#[Controller(IndexAction::class)]
class UserController
{

  #[RequestMap(uri: "/admin/user/{username}/{id}", method: 'GET')]
  public function index(HttpRequest $request, $username, $id)
  {
    return var_dump_r($username, $id);
    // return json_encode(['username' => $username, 'id' => $id,  'xxxx' => 1]);
  }
}