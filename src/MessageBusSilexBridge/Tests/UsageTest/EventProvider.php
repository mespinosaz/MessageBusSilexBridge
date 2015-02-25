<?php

namespace mespinosaz\MessageBusSilexBridge\Tests\UsageTest;

use mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Event\Handler\WhenActionWasDoneDoSomething;
use mespinosaz\MessageBusSilexBridge\Tests\UsageTest\Event\Handler\WhenActionWasDoneDoSomethingElse;
use Silex\Application;
use Silex\ServiceProviderInterface;

class EventProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $this->registerMap($app);
        $this->registerHandlers($app);
    }

    public function boot(Application $app)
    {
    }

    /**
     * @param Application $app
     */
    private function registerMap(Application $app)
    {
        $app['event_map'] = $app->share(function () use ($app) {
            return array(
                'action_was_done' => array(
                    'when_action_was_done_do_something',
                    'when_action_was_done_do_something_else'
                )
            );
        });
    }

    /**
     * @param Application $app
     */
    private function registerHandlers(Application $app)
    {
        $app['event_handlers'] = $app->share(function () use ($app) {
            return array(
                'when_action_was_done_do_something' => new WhenActionWasDoneDoSomething(),
                'when_action_was_done_do_something_else' => new WhenActionWasDoneDoSomethingElse()
            );
        });
    }
} 