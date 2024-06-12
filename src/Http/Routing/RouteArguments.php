<?php

namespace Takemo101\Chubby\Http\Routing;

readonly class RouteArguments
{
    /**
     * constructor
     *
     * @param array<string,string> $arguments
     */
    public function __construct(
        private array $arguments = [],
    ) {
        //
    }

    /**
     * Get the arguments.
     *
     * @return array<string,string>
     */
    public function arguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get the argument.
     *
     * @param string $key
     * @return string|null
     */
    public function get(string $key): ?string
    {
        return $this->arguments[$key] ?? null;
    }

    /**
     * Check if the argument exists.
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->arguments);
    }

    /**
     * Join the arguments.
     *
     * @param self $arguments
     * @return self
     */
    public function join(self $arguments): self
    {
        return new self([
            ...$this->arguments,
            ...$arguments->arguments(),
        ]);
    }

    /**
     * Create an instance from the arguments.
     *
     * @param array<string,string> $arguments
     * @param boolean $urlDecode
     * @return self
     */
    public static function create(
        array $arguments,
        bool $urlDecode = true,
    ): self {
        return new self(
            $urlDecode
                ? array_map('rawurldecode', $arguments)
                : $arguments,
        );
    }
}
