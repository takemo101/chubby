<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Event\EventDispatcher;
use Takemo101\Chubby\Event\EventListenerProvider;
use Takemo101\Chubby\Event\EventRegister;
use Takemo101\Chubby\Hook\Hook;

/**
 * Event related.
 */
class EventProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'event';

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        $definitions->add(
            [
                EventRegister::class => function (
                    ConfigRepository $config,
                    Hook $hook,
                ) {
                    $listen = $config->get('event.listen', []);

                    $register = EventRegister::fromArray($listen);

                    $hook->doTyped($register);

                    return $register;
                },
                EventDispatcherInterface::class => function (
                    ConfigRepository $config,
                    ContainerInterface $container,
                ) {
                    /** @var class-string<EventDispatcherInterface> */
                    $class = $config->get(
                        'event.dispatcher',
                        EventDispatcher::class,
                    );

                    /** @var EventDispatcherInterface */
                    $dispatcher = $container->get($class);

                    return $dispatcher;
                },
                ListenerProviderInterface::class =>
                function (
                    ConfigRepository $config,
                    ContainerInterface $container,
                ) {
                    /** @var class-string<ListenerProviderInterface> */
                    $class = $config->get(
                        'event.provider',
                        EventListenerProvider::class,
                    );

                    /** @var ListenerProviderInterface */
                    $provider = $container->get($class);

                    return $provider;
                },
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        //
    }
}
