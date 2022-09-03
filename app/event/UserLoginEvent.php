<?php
declare(strict_types=1);
namespace app\event;

use app\entity\User;

class UserLoginEvent
{
    /**
     * @param string $userId
     * @return void
     */
    public function demo(string $userId)
    {
        //---exec start---
        //---example update insert, or deliver message----
        $count = User::query()->count();
        var_dump($count);
        //---exec end---
        echo "UserLoginEvent->ex,userId:{$userId}".PHP_EOL;
    }

    /**
     * static method
     * @param string $userId
     * @return void
     */
    public static function demo2(string $userId)
    {
        //---exec start---
        //---example update insert, or deliver message----
        $count = User::query()->count();
        var_dump($count);
        //---exec end---
        echo "UserLoginEvent->ex2,userId:{$userId}".PHP_EOL;
    }
}
