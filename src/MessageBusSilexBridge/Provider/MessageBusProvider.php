<?php

namespace mespinosaz\MessageBusSilexBridge\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use SimpleBus\Message\Bus\Middleware\MessageBusSupportingMiddleware;
use SimpleBus\Message\Handler\DelegatesToMessageHandlerMiddleware;
use SimpleBus\Message\Handler\Resolver\NameBasedMessageHandlerResolver;
use SimpleBus\Message\Name\NamedMessageNameResolver;
use SimpleBus\Message\Handler\Map\LazyLoadingMessageHandlerMap;
use SimpleBus\Message\Recorder\PublicMessageRecorder;
use SimpleBus\Message\Bus\Middleware\FinishesHandlingMessageBeforeHandlingNext;
use SimpleBus\Message\Subscriber\NotifiesMessageSubscribersMiddleware;
use SimpleBus\Message\Subscriber\Resolver\NameBasedMessageSubscriberResolver;
use SimpleBus\Message\Subscriber\Collection\LazyLoadingMessageSubscriberCollection;
use SimpleBus\Message\Recorder\HandlesRecordedMessagesMiddleware;

class MessageBusProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
        $this->registerCommandBus($app);
        $this->registerEventBus($app);
        $this->registerMiddlewares($app);
        $this->registerMessageResolvers($app);
        $this->registerLazyMessageLoaders($app);
        $this->registerServiceLocators($app);
        $this->registerEventRecorder($app);
        $this->registerMessageMapping($app);
    }

    public function boot(Application $app)
    {
    }

    /**
     * @param Application $app
     */
    private function registerCommandBus(Application $app)
    {
        $app['command_bus'] = $app->share(function () use ($app) {
            $bus = new MessageBusSupportingMiddleware();
            $bus->appendMiddleware(clone $app['finishes_handling_message_before_handling_next']);
            $bus->appendMiddleware($app['delegates_to_message_handler_middleware']);
            $bus->appendMiddleware($app['handles_recorded_messages_middleware']);
            return $bus;
        });


    }

    /**
     * @param Application $app
     */
    private function registerServiceLocators(Application $app)
    {
        $app['event_handler_locator'] = $app->share(function () use ($app) {
            return function ($serviceId) use ($app) {
                return $app['event_handlers'][$serviceId];
            };
        });

        $app['command_handler_locator'] = $app->share(function () use ($app) {
            return function ($serviceId) use ($app) {
                return $app['command_handlers'][$serviceId];
            };
        });
    }

    /**
     * @param Application $app
     */
    private function registerEventBus(Application $app)
    {
        $app['event_bus'] = $app->share(function () use ($app) {
            $bus = new MessageBusSupportingMiddleware();
            $bus->appendMiddleware(clone $app['finishes_handling_message_before_handling_next']);
            $bus->appendMiddleware($app['notifies_message_subscribers_middleware']);

            return $bus;
        });
    }

    /**
     * @param Application $app
     */
    private function registerMiddlewares(Application $app)
    {
        $app['finishes_handling_message_before_handling_next'] = $app->share(function () use ($app) {
            return new FinishesHandlingMessageBeforeHandlingNext();
        });

        $app['delegates_to_message_handler_middleware'] = $app->share(function () use ($app) {
            return new DelegatesToMessageHandlerMiddleware($app['name_based_message_handler_resolver']);
        });

        $app['handles_recorded_messages_middleware'] = $app->share(function () use ($app) {
            return new HandlesRecordedMessagesMiddleware(
                $app['public_event_recorder'],
                $app['event_bus']
            );
        });

        $app['notifies_message_subscribers_middleware'] = $app->share(function () use ($app) {
            return new NotifiesMessageSubscribersMiddleware($app['name_based_message_subscriber_resolver']);
        });
    }

    /**
     * @param Application $app
     */
    private function registerMessageResolvers(Application $app)
    {
        $app['name_based_message_handler_resolver'] = $app->share(function () use ($app) {
            return new NameBasedMessageHandlerResolver(
                $app['named_message_name_resolver'],
                $app['lazy_loading_message_handler_map']
            );
        });
        $app['name_based_message_subscriber_resolver'] = $app->share(function () use ($app) {
            return new NameBasedMessageSubscriberResolver(
                $app['named_message_name_resolver'],
                $app['lazy_loading_message_subscriber_collection']
            );
        });
        $app['named_message_name_resolver'] = $app->share(function () use ($app) {
            return new NamedMessageNameResolver();
        });
    }

    /**
     * @param Application $app
     */
    private function registerLazyMessageLoaders(Application $app)
    {
        $app['lazy_loading_message_handler_map'] = $app->share(function () use ($app) {
            return new LazyLoadingMessageHandlerMap(
                $app['command_map'],
                $app['command_handler_locator']
            );
        });

        $app['lazy_loading_message_subscriber_collection'] = $app->share(function () use ($app) {
            return new LazyLoadingMessageSubscriberCollection(
                $app['event_map'],
                $app['event_handler_locator']
            );
        });
    }

    /**
     * @param Application $app
     */
    private function registerEventRecorder(Application $app)
    {
        $app['public_event_recorder'] = $app->share(function () use ($app) {
            return new PublicMessageRecorder();
        });
    }

    /**
     * @param Application $app
     */
    private function registerMessageMapping(Application $app)
    {
        $app['command_map'] = $app->share(function () use ($app) {
            return array();
        });

        $app['command_handlers'] = $app->share(function () use ($app) {
            return array();
        });

        $app['event_map'] = $app->share(function () use ($app) {
            return array();
        });

        $app['event_handlers'] = $app->share(function () use ($app) {
            return array();
        });
    }
}
