<?php

namespace Takemo101\Chubby\Http\Routing;

use InvalidArgumentException;
use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class DomainRoute
{
    /**
     * @var Closure
     */
    private readonly Closure $handler;

    /**
     * constructor
     *
     * @param string $pattern
     * @param callable(ServerRequestInterface):RequestHandlerInterface $handler
     */
    public function __construct(
        private readonly string $pattern,
        callable $handler,
        private ?string $name = null,
    ) {
        if (empty($pattern)) {
            throw new InvalidArgumentException('pattern is empty');
        }

        $this->handler = $handler instanceof Closure
            ? $handler
            : Closure::fromCallable($handler);
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
     * Get route handler
     *
     * @return Closure
     */
    public function getHandler(): Closure
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
