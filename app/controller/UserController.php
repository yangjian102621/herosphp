<?php

namespace app\controller;

use herosphp\core\HttpRequest;

//#[Controller(IndexAction::class)]
class UserController
{
  public function index(HttpRequest $request)
  {
    var_dump($request->uri());
    var_dump($request->path());
  }
}
