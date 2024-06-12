<?php

namespace Takemo101\Chubby\Support;

use Psr\Http\Message\UriInterface;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\Http\Uri\ApplicationUri;

/**
 * Application summary data
 */
readonly class ApplicationSummary
{
    /**
     * @var string
     */
    public string $env;

    /**
     * constructor
     *
     * @param ApplicationUri $uri
     * @param string $name
     * @param string $env local | development | production
     * @param boolean $debug
     * @param boolean $builtInServer
     */
    public function __construct(
        public ApplicationUri $uri,
        public string $name = Application::Name,
        string $env = 'local',
        public bool $debug = true,
        public bool $builtInServer = false,
    ) {
        $this->env = strtolower($env);
    }

    /**
     * Get application uri.
     *
     * @return ApplicationUri
     */
    public function getUri(): ApplicationUri
    {
        return $this->uri;
    }

    /**
     * Get application name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get debug mode.
     *
     * @return boolean
     */
    public function isDebugMode(): bool
    {
        return $this->debug;
    }

    /**
     * Get built-in server flag.
     *
     * @return boolean
     */
    public function isBuiltInServer(): bool
    {
        return $this->builtInServer;
    }

    /**
     * Get environment.
     *
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->env;
    }

    /**
     * Has environment?
     *
     * @param string ...$environments
     * @return boolean
     */
    public function hasEnvironment(string ...$environments): bool
    {
        /** @var string[] */
        $envs = array_map(
            fn ($env) => strtolower($env),
            $environments,
        );

        return in_array(
            $this->env,
            $envs,
        );
    }
}
