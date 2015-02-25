<?php

namespace mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Command;

use SimpleBus\Message\Name\NamedMessage;
use SimpleBus\Message\Type\Command;

class DoOtherAction implements Command, NamedMessage
{
    public static function messageName()
    {
        return 'do_other_action';
    }
}
