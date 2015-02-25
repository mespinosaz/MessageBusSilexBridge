<?php

namespace mespinosaz\MessageBusSilexBridge\Tests\UsageTest;

use mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Command\Handler\DoActionHandler;
use Silex\Application;
use Silex\ServiceProviderInterface;

class CommandProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $this->registerMap($app);
        $this->registerHandlers($app);
    }

    public function boot(Application $app)
    {
    }

    private function registerMap(Application $app)
    {
        $app['command_map'] = $app->share(function () use ($app) {
            return array(
                'do_action' => 'do_action_handler'
            );
        });

    }

    private function registerHandlers(Application $app)
    {
        $app['command_handlers'] = $app->share(function () use ($app) {
            return array(
                'do_action_handler' => new DoActionHandler($app['public_event_recorder'])
            );
        });
    }
}