<?php

namespace Takemo101\Chubby\Support;

/**
 * Application summary data
 */
final readonly class ApplicationSummary
{
    /**
     * @var string
     */
    public string $env;

    /**
     * constructor
     *
     * @param string $env local | development | production
     * @param boolean $debug
     */
    public function __construct(
        string $env = 'local',
        public bool $debug = true,
    ) {
        $this->env = strtolower($env);
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
