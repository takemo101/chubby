<?php

namespace Takemo101\Chubby\Http\Routing;

use Takemo101\Chubby\Http\Support\AbstractContext;

final class DomainRouteContext extends AbstractContext
{
    /** @var string */
    public const ContextKey = '__domain__';

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
}
