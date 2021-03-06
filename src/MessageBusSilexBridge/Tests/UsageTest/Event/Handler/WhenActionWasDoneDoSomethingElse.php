<?php

namespace mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Event\Handler;

use SimpleBus\Message\Message;
use SimpleBus\Message\Subscriber\MessageSubscriber;

class WhenActionWasDoneDoSomethingElse  implements MessageSubscriber
{
    public function notify(Message $message)
    {
        $this->eventHandled = true;
    }
}