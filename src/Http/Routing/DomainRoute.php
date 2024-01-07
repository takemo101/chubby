<?php

namespace Takemo101\Chubby\Http\Routing;

use InvalidArgumentException;
use Psr\Http\Server\RequestHandlerInterface;

class DomainRoute
{
    /**
     * constructor
     *
     * @param string $pattern
     * @param RequestHandlerInterface $handler
     * @param string|null $name
     */
    public function __construct(
        private readonly string $pattern,
        private readonly RequestHandlerInterface $handler,
        private ?string $name = null,
    ) {
        if (empty($pattern)) {
            throw new InvalidArgumentException('pattern is empty');
        }
    }

    /**
     * Get route pattern
     *
     * @return string
     */
    public function getPattern(): string
    {
        return $this->pattern;
    }

    /**
     * Get route request handler
     *
     * @return RequestHandlerInterface
     */
    public function getRequestHandler(): RequestHandlerInterface
    {
        return $this->handler;
    }

    /**
     * Get route name
     *
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set route name
     *
     * @param string $name
     * @return self
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
