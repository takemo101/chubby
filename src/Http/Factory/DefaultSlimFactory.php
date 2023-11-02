<?php

namespace Takemo101\Chubby\Http\Factory;

use Psr\Container\ContainerInterface;
use Slim\App as Slim;
use Slim\Factory\AppFactory;
use Slim\Interfaces\InvocationStrategyInterface;

final readonly class DefaultSlimFactory implements SlimFactory
{
    /**
     * constructor
     *
     * @param ContainerInterface $container
     * @param InvocationStrategyInterface $invocationStrategy
     */
    public function __construct(
        private ContainerInterface $container,
        private InvocationStrategyInterface $invocationStrategy,
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

        return $app;
    }
}
