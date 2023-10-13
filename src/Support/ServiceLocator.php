<?php

namespace Takemo101\Chubby\Support;

use Psr\Log\LoggerInterface;
use RuntimeException;
use Slim\App as Slim;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Console\SymfonyConsoleAdapter;
use Takemo101\Chubby\Hook\Hook;

/**
 * Have global access to your application's services
 */
final class ServiceLocator
{
    /**
     * @var self|null
     */
    private static ?self $instance = null;

    /**
     * constructor
     *
     * @param Application $app
     */
    private function __construct(
        private readonly Application $app,
    ) {
        //
    }

    /**
     * Initialize singleton application instance.
     *
     * @return self
     */
    public static function initialize(Application $app): void
    {
        self::$instance = new self($app);
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
     * Get application.
     *
     * @return Application
     */
    public static function app(): Application
    {
        return self::instance()->app;
    }

    /**
     * Get environment.
     *
     * @return Environment
     */
    public static function env(): Environment
    {
        /** @var Environment */
        $env = self::app()->get(Environment::class);

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
        $config = self::app()->get(ConfigRepository::class);

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
        $logger = self::app()->get(LoggerInterface::class);

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
        $path = self::app()->get(ApplicationPath::class);

        return $path;
    }

    /**
     * Get slim application.
     *
     * @return Slim
     */
    public static function slim(): Slim
    {
        /** @var Slim */
        $slim = self::app()->get(Slim::class);

        return $slim;
    }

    /**
     * Get console application.
     *
     * @return SymfonyConsoleAdapter
     */
    public static function console(): SymfonyConsoleAdapter
    {
        /** @var SymfonyConsoleAdapter */
        $console = self::app()->get(SymfonyConsoleAdapter::class);

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
        $hook = self::app()->get(Hook::class);

        return $hook;
    }
}
