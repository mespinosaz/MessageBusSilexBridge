<?php

namespace mespinosaz\MessageBusSilexBridge\Tests;

use mespinosaz\MessageBusSilexBridge\Provider\MessageBusProvider;
use mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Command\DoAction;
use mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Command\DoOtherAction;
use mespinosaz\MessageBusSilexBridge\Tests\UsageTest\CommandProvider;
use mespinosaz\MessageBusSilexBridge\Tests\UsageTest\EventProvider;
use Silex\Application;

class UsageTest extends \PHPUnit_Framework_TestCase
{
    public function testCommandsAndEvents()
    {

        $app = $this->createApplication();

        $app['command_bus']->handle(new DoAction());

        $commandHandlerName = $app['command_map']['do_action'];
        $commandHandler = $app['command_handlers'][$commandHandlerName];
        $this->assertTrue($commandHandler->commandHandled);

        $eventHandlersName = $app['event_map']['action_was_done'];

        foreach($eventHandlersName as $eventHandlerName) {
            $eventHandler = $app['event_handlers'][$eventHandlerName];
            $this->assertTrue($eventHandler->eventHandled);
        }
    }

    /**
     * @expectedException \SimpleBus\Message\Handler\Map\Exception\NoHandlerForMessageName
     */
    public function testNotHandledCommand()
    {
        $app = $this->createApplication();
        $app['command_bus']->handle(new DoOtherAction());
    }

    private function createApplication()
    {
        $app = new Application();
        $app->register(new MessageBusProvider());
        $app->register(new CommandProvider());
        $app->register(new EventProvider());
        return $app;
    }
} 