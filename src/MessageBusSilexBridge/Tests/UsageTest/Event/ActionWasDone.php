<?php

namespace mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Event;

use SimpleBus\Message\Name\NamedMessage;
use SimpleBus\Message\Type\Event;

class ActionWasDone implements Event, NamedMessage
{
    public static function messageName()
    {
        return 'action_was_done';
    }
}
