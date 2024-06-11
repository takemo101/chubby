<?php

namespace Takemo101\Chubby\Support;

use Dotenv\Repository\RepositoryInterface;

/**
 * Get environment.
 */
class Environment
{
    /**
     * constructor
     *
     * @param RepositoryInterface $repository
     * @param EnvironmentStringParser $parser
     */
    public function __construct(
        private readonly RepositoryInterface $repository,
        private readonly EnvironmentStringParser $parser = new EnvironmentStringParser(),
    ) {
        //
    }

    /**
     * Get environment value.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, $default = null): mixed
    {
        /** @var string|null */
        $value = $this->repository->get(
            strtoupper($key),
        );

        return $value === null
            ? $default
            : $this->parser->parse($value);
    }
}
