<?php

namespace app\controller;

use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\annotation\Post;
use herosphp\annotation\RequestMap;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;
use Workerman\Protocols\Http\Response;

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
  public function get(HttpRequest $request): Response
  {
    return $this->jsonView(0, ['hello' => 'world']);
  }
}
