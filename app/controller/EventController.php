<?php

namespace app\controller;

use herosEvent\Event;
use herosphp\annotation\Controller;
use herosphp\annotation\Get;

#[Controller(name: EventController::class)]
class EventController
{
    #[Get(uri: '/event/ex', desc: 'demo')]
    public function ex(): string
    {
        Event::emit('user.login', 'abc');
        return 'ok';
    }
}
