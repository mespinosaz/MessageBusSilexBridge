<?php

namespace mespinosaz\MessageBusSilexBridge\Tests;

use mespinosaz\MessageBusSilexBridge\Provider\MessageBusProvider;
use Silex\Application;

class ProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testRegister()
    {
        $app = new Application();
        $app->register(new MessageBusProvider());

        $this->assertInstanceOf('\SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware', $app['command_bus']);
        $this->assertInstanceOf('\SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware', $app['event_bus']);
        $this->assertInstanceOf('\SimpleBus\Message\Recorder\RecordsMessages', $app['public_event_recorder']);
    }
}