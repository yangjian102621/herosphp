<?php

declare(strict_types=1);

namespace process;

use app\init\LaravelStarter;
use herosphp\core\Config;

//需要用到什么启动器，用户自己加载
//启动注册laravel mysql
//LaravelStarter::init(Config::get(name:'database', default: []));

class AsyncTask
{
    public function run(): string
    {
        $count = 2;
        //echo  date('Y-m-d H:i:s')."-i am running，async worker db query:{$count}".PHP_EOL;
        return 'success';
    }
}
