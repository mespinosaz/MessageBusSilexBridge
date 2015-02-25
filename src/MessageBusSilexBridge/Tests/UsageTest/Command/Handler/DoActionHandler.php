<?php

namespace mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Command\Handler;

use mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Event\ActionWasDone;
use SimpleBus\Message\Handler\MessageHandler;
use SimpleBus\Message\Message;
use SimpleBus\Message\Recorder\PrivateMessageRecorderCapabilities;
use SimpleBus\Message\Recorder\PublicMessageRecorder;

class DoActionHandler implements MessageHandler
{
    private $recorder;

    public function __construct(PublicMessageRecorder $recorder)
    {
        $this->recorder = $recorder;
    }

    public function handle(Message $message)
    {
        $this->commandHandled = true;
        $this->recorder->record(new ActionWasDone());
    }
}
