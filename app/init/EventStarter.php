<?php
declare(strict_types=1);
namespace app\init;

use herosphp\core\Config;
use herosphp\utils\Logger;

class EventStarter extends \herosEvent\EventStarter
{
    protected static bool $debugList = true;
}

try {
    EventStarter::init(Config::get('event', null, []));
} catch (\ReflectionException $e) {
    Logger::error($e->getMessage());
}
