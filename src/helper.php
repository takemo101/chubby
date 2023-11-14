<?php

use Monolog\Level;
use Psr\Log\LoggerInterface;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Console\SymfonyConsoleAdapter;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\SlimHttpAdapter;
use Takemo101\Chubby\Support\ServiceLocator;

if (!function_exists('container')) {
    /**
     * Access the application container.
     *
     * @return ApplicationContainer
     */
    function container(): ApplicationContainer
    {
        return ServiceLocator::container();
    }
}

if (!function_exists('env')) {
    /**
     * Get enviroment variables
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function env(string $key, $default = null)
    {
        return ServiceLocator::env()->get($key, $default);
    }
}

if (!function_exists('config')) {
    /**
     * Get config data
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    function config(string $key, $default = null)
    {
        return ServiceLocator::config()->get($key, $default);
    }
}

if (!function_exists('log')) {
    /**
     * Write log.
     *
     * @param string|null $message
     * @param mixed[] $context
     * @param Level $level
     * @return LoggerInterface
     */
    function log(
        ?string $message = null,
        array $context = [],
        Level $level = Level::Info,
    ): LoggerInterface {
        $logger = ServiceLocator::logger();

        if ($message) {
            $logger->log($level, $message, $context);
        }

        return $logger;
    }
}

if (!function_exists('base_path')) {
    /**
     * Get application base path
     *
     * @param string ...$paths
     * @return string
     */
    function base_path(
        string ...$paths,
    ): string {
        $appPath = ServiceLocator::path();

        return $appPath->getBasePath(...$paths);
    }
}

if (!function_exists('storage_path')) {
    /**
     * Get application storage path
     *
     * @param string ...$paths
     * @return string
     */
    function storage_path(
        string ...$paths,
    ): string {
        $appPath = ServiceLocator::path();

        return $appPath->getStoragePath(...$paths);
    }
}

if (!function_exists('http')) {
    /**
     * Get http application.
     *
     * @return SlimHttpAdapter
     */
    function http(): SlimHttpAdapter
    {
        return ServiceLocator::http();
    }
}

if (!function_exists('console')) {
    /**
     * Get console application.
     *
     * @return SymfonyConsoleAdapter
     */
    function console(): SymfonyConsoleAdapter
    {
        return ServiceLocator::console();
    }
}

if (!function_exists('hook')) {
    /**
     * Get hook.
     *
     * @return Hook
     */
    function hook(): Hook
    {
        return ServiceLocator::hook();
    }
}
