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
    var_dump($username);
    var_dump($id);
    return json_encode(['username' => $username, 'id' => $id]);
  }
}
