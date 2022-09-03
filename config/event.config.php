<?php
return [
    //key=> callback array
    'user.login' => [
        function (string $userId) {
            echo "config.user.login event,userId:{$userId}".PHP_EOL;
        },
        [\app\event\UserLoginEvent::class,'demo'],
        //static method
        [\app\event\UserLoginEvent::class,'demo2'],
    ],
];
