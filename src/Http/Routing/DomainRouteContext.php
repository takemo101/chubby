<?php

namespace Takemo101\Chubby\Http\Routing;

use Takemo101\Chubby\Http\Context\AbstractContext;

class DomainRouteContext extends AbstractContext
{
    /** @var string */
    public const ContextKey = self::class;

    /**
     * constructor
     *
     * @param RouteArguments $arguments
     */
    public function __construct(
        private readonly RouteArguments $arguments = new RouteArguments(),
    ) {
        //
    }

    /**
     * Get domain route arguments
     *
     * @return RouteArguments
     */
    public function getArguments(): RouteArguments
    {
        return $this->arguments;
    }
}
