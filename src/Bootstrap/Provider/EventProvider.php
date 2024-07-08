<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as SymfonyEventDispatcherInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Bootstrap\Support\ConfigBasedDefinitionReplacer;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Event\EventDispatcher;
use Takemo101\Chubby\Event\EventListenerProvider;
use Takemo101\Chubby\Event\EventRegister;
use Takemo101\Chubby\Event\ListenerProvider;
use Takemo101\Chubby\Hook\Hook;

use function DI\get;

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
                    /** @var class-string[] */
                    $listen = $config->get('event.listeners', []);

                    $register = EventRegister::fromArray($listen);

                    $hook->doTyped($register, true);

                    return $register;
                },
                EventDispatcherInterface::class => function (
                    SymfonyEventDispatcherInterface $dispatcher,
                    Hook $hook,
                ) {
                    /** @var EventDispatcherInterface */
                    $dispatcher = $hook->do(
                        tag: EventDispatcherInterface::class,
                        parameter: $dispatcher,
                        delayed: true,
                    );

                    return $dispatcher;
                },
                ListenerProviderInterface::class => get(ListenerProvider::class),
                ...ConfigBasedDefinitionReplacer::createDependencyDefinitions(
                    dependencies: [
                        SymfonyEventDispatcherInterface::class => EventDispatcher::class,
                        ListenerProvider::class => EventListenerProvider::class,
                    ],
                    configKeyPrefix: 'event',
                )
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
