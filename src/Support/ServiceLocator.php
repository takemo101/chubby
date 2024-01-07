<?php

namespace Takemo101\Chubby\Support;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use RuntimeException;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Console\SymfonyConsole;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\SlimHttp;

/**
 * Have global access to your application's services
 */
class ServiceLocator
{
    /**
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * constructor
     *
     * @param ApplicationContainer $container
     */
    private function __construct(
        private readonly ApplicationContainer $container,
    ) {
        //
    }

    /**
     * Initialize singleton application instance.
     *
     * @return void
     */
    public static function initialize(ApplicationContainer $container): void
    {
        self::$instance = new self($container);
    }

    /**
     * Get a singleton instance.
     *
     * @return self
     */
    private static function instance(): self
    {
        $instance = self::$instance;

        return $instance ?? throw new RuntimeException('ServiceLocator is not initialized');
    }

    /**
     * Get application container.
     *
     * @return ApplicationContainer
     */
    public static function container(): ApplicationContainer
    {
        return self::instance()->container;
    }

    /**
     * Get environment.
     *
     * @return Environment
     */
    public static function env(): Environment
    {
        /** @var Environment */
        $env = self::container()->get(Environment::class);

        return $env;
    }

    /**
     * Get config repository.
     *
     * @return ConfigRepository
     */
    public static function config(): ConfigRepository
    {
        /** @var ConfigRepository */
        $config = self::container()->get(ConfigRepository::class);

        return $config;
    }

    /**
     * Get logger.
     *
     * @return LoggerInterface
     */
    public static function logger(): LoggerInterface
    {
        /** @var LoggerInterface */
        $logger = self::container()->get(LoggerInterface::class);

        return $logger;
    }

    /**
     * Get application path.
     *
     * @return ApplicationPath
     */
    public static function path(): ApplicationPath
    {
        /** @var ApplicationPath */
        $path = self::container()->get(ApplicationPath::class);

        return $path;
    }

    /**
     * Get http application.
     *
     * @return SlimHttp
     */
    public static function http(): SlimHttp
    {
        /** @var SlimHttp */
        $http = self::container()->get(SlimHttp::class);

        return $http;
    }

    /**
     * Get console application.
     *
     * @return SymfonyConsole
     */
    public static function console(): SymfonyConsole
    {
        /** @var SymfonyConsole */
        $console = self::container()->get(SymfonyConsole::class);

        return $console;
    }

    /**
     * Get hook.
     *
     * @return Hook
     */
    public static function hook(): Hook
    {
        /** @var Hook */
        $hook = self::container()->get(Hook::class);

        return $hook;
    }

    /**
     * Get event dispatcher.
     *
     * @return EventDispatcherInterface
     */
    public static function event(): EventDispatcherInterface
    {
        /** @var EventDispatcherInterface */
        $event = self::container()->get(EventDispatcherInterface::class);

        return $event;
    }
}
