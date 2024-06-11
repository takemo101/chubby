<?php

namespace Takemo101\Chubby\Support;

/**
 * Get environment variable from external source.
 */
class ExternalEnvironmentAccessor
{
    /**
     * constructor
     *
     * @param EnvironmentStringParser $parser
     */
    public function __construct(
        private readonly EnvironmentStringParser $parser = new EnvironmentStringParser(),
    ) {
        //
    }

    /**
     * Get environment variable.
     *
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        /** @var string|false */
        $value = getenv($key);

        return $value === false
            ? $default
            : $this->parser->parse($value);
    }
}
