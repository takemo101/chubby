<?php

namespace Takemo101\Chubby\Http\Factory;

use Psr\Container\ContainerInterface;
use Slim\App as Slim;
use Slim\Factory\AppFactory;
use Slim\Interfaces\InvocationStrategyInterface;
use Takemo101\Chubby\Http\GlobalMiddlewareCollection;

class DefaultSlimFactory implements SlimFactory
{
    /**
     * constructor
     *
     * @param ContainerInterface $container
     * @param InvocationStrategyInterface $invocationStrategy
     * @param GlobalMiddlewareCollection $middlewares
     */
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly InvocationStrategyInterface $invocationStrategy,
        private readonly GlobalMiddlewareCollection $middlewares,
    ) {
        //
    }

    /**
     * Create Slim application.
     *
     * @return Slim
     */
    public function create(): Slim
    {
        $app = AppFactory::createFromContainer($this->container);

        $app->getRouteCollector()
            ->setDefaultInvocationStrategy($this->invocationStrategy);

        foreach ($this->middlewares->classes() as $middleware) {
            $app->add($middleware);
        }

        return $app;
    }
}
