<?php

declare(strict_types=1);

namespace app\controller;

use app\entity\User;
use app\init\LaravelStarter;
use herosLdb\Db;
use herosphp\annotation\Controller;
use herosphp\annotation\Get;
use herosphp\core\BaseController;
use herosphp\core\Config;

#[Controller(DBController::class)]
class DBController extends BaseController
{
    public function __init()
    {
        parent::__init();
        LaravelStarter::init(Config::get(name: 'database', default: []));
    }

    #[Get(uri: '/db/query', desc: '查询')]
    public function index(): string
    {
        $str = '';
        $count = Db::connection('default')->table('user')->count();
        $str .= "table:user,count:{$count}<br>";
        $users = User::query()->get();
        /** @var User $user*/
        foreach ($users ?? [] as $user) {
            $str .= "id:{$user->id},email:{$user->email}<br>";
        }

        return $str;
    }

    /**
     * @return string
     * http://127.0.0.1:2345/db/page?page=1
     * http://127.0.0.1:2345/db/page?page=2
     */
    #[Get(uri: '/db/page')]
    public function page(): string
    {
        $str = '';
        $users = User::query()->paginate(1);
        /** @var User $user*/
        foreach ($users ?? [] as $user) {
            $str .= "id:{$user->id},email:{$user->email}<br>";
        }

        return $str;
    }
}