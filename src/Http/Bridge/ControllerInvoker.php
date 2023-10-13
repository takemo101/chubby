<?php

namespace Takemo101\Chubby\Http\Bridge;

use Takemo101\Chubby\Http\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Takemo101\Chubby\Hook\Hook;

final class ControllerInvoker implements InvocationStrategyInterface
{
    /**
     * constructor
     *
     * @param InvocationStrategyInterface $invoker
     * @param Hook $hook
     */
    public function __construct(
        private InvocationStrategyInterface $invoker,
        private Hook $hook,
    ) {
        //
    }

    /**
     * Invoke a route callable.
     *
     * @param callable               $callable       The callable to invoke using the strategy.
     * @param ServerRequestInterface $request        The request object.
     * @param ResponseInterface      $response       The response object.
     * @param array                  $routeArguments The route's placeholder arguments
     *
     * @return ResponseInterface|string The response from the callable.
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        /** @var ServerRequestInterface */
        $request = $this->hook->apply(
            ServerRequestInterface::class,
            $request,
        );

        $this->hook->doActionByObject(
            new Context($request, $response),
        );

        $output = $this->invoker->__invoke(
            $callable,
            $request,
            $response,
            $routeArguments,
        );

        /** @var ResponseInterface */
        $response = $this->hook->apply(
            ResponseInterface::class,
            $output,
        );

        return $response;
    }
}
