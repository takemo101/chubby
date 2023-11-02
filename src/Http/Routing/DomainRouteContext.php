<?php

namespace Takemo101\Chubby\Http\Routing;

use Psr\Http\Message\ServerRequestInterface;

final readonly class DomainRouteContext
{
    public const DomainRouteArguments = '__domain__';

    /**
     * @var array<string,string>
     */
    private array $arguments;

    /**
     * constructor
     *
     * @param array<string,string> $arguments
     */
    public function __construct(
        array $arguments = [],
    ) {
        $this->arguments = $arguments;
    }

    /**
     * Get domain route arguments
     *
     * @return array<string,string>
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get request with contextual data.
     *
     * @param ServerRequestInterface $request
     */
    public function composeRequest(ServerRequestInterface $request): ServerRequestInterface
    {
        return $request->withAttribute(self::DomainRouteArguments, $this->arguments);
    }

    /**
     * Create a context instance from a ServerRequest
     *
     * @param ServerRequestInterface $request
     * @return self
     */
    public static function fromRequest(ServerRequestInterface $request): self
    {
        /** @var array<string,string> */
        $arguments = $request->getAttribute(self::DomainRouteArguments, []);

        return new self($arguments);
    }
}
