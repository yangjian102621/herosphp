<?php

declare(strict_types=1);

namespace app\controller;

use app\service\UserService;
use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\annotation\Inject;
use herosphp\annotation\Post;
use herosphp\annotation\RequestMap;
use herosphp\core\BaseController;
use herosphp\core\HttpRequest;
use herosphp\core\HttpResponse;
use herosphp\utils\Logger;

#[Controller(IndexAction::class)]
class UserController extends BaseController
{
    #[Inject(UserService::class)]
    protected UserService $userService;

    #[RequestMap(uri: ['/', '/index'], method: 'GET')]
    public function index(HttpRequest $request): HttpResponse
    {
        return $this->view('index', ['title' => 'Hello, world!']);
    }

    #[RequestMap(uri: '/user/{username}', method: 'GET')]
    public function var(HttpRequest $request, $username)
    {
        Logger::info('Not implemented yet.');
        Logger::warn('Not implemented yet.');
        Logger::error('Not implemented yet.');
        return var_dump_r($username, $request);
    }

    #[Get(uri: ['/news/get', '/news/fetch'])]
    public function get(HttpRequest $request): HttpResponse
    {
        return $this->json(['code' => 0, 'message' => 'hello world', 'var' => $request->get('var')]);
    }

    #[Post(uri: '/user/add')]
    public function post(): string
    {
        return 'Not implemented yet.';
    }
}
