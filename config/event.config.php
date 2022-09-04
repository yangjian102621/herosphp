<?php

use app\event\UserLoginEvent;

return [
    //key=> callback array
    'user.login' => [
        function (string $userId) {
            echo "config.user.login event,userId:{$userId}".PHP_EOL;
        },
        [UserLoginEvent::class,'demo'],
        //static method
        [UserLoginEvent::class,'demo2'],
    ],
];
