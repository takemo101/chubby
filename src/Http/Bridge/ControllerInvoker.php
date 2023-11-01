<?php

namespace Takemo101\Chubby\Http\Bridge;

use Invoker\InvokerInterface;
use Takemo101\Chubby\Http\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\ResponseTransformer\ResponseTransformers;
use Takemo101\Chubby\Http\Routing\DomainRouteContext;

final readonly class ControllerInvoker implements InvocationStrategyInterface
{
    /**
     * constructor
     *
     * @param InvokerInterface $invoker
     */
    public function __construct(
        private InvokerInterface $invoker,
        private ResponseTransformers $transformers,
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
     * @param array<string,string>   $routeArguments The route's placeholder arguments
     *
     * @return ResponseInterface The response from the callable.
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ): ResponseInterface {
        /** @var mixed */
        $data = $this->invoker->call(
            $callable,
            $this->getInjectParameters($request, $response, $routeArguments),
        );

        $transformedResponse = $this->transformers->transform(
            $data,
            $request,
            $response,
        );

        /** @var ResponseInterface */
        $hookedResponse = $this->hook->filter(
            ResponseInterface::class,
            $transformedResponse ?? $response,
        );

        return $hookedResponse;
    }

    /**
     * Get inject the request and response and route arguments and attributes and etc..
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array<string,string> $routeArguments
     * @return mixed[]
     */
    private function getInjectParameters(
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments,
    ): array {
        /** @var ServerRequestInterface */
        $hookedRequest = $this->hook->filter(
            ServerRequestInterface::class,
            $this->injectRouteArguments($request, $routeArguments),
        );

        $domainRouteContext = DomainRouteContext::fromRequest($hookedRequest);

        $routeArguments = [
            ...$domainRouteContext->getArguments(),
            ...$routeArguments,
        ];

        $context = new Context(
            request: $hookedRequest,
            response: $response,
            routeArguments: $routeArguments,
        );

        return [
            'context' => $context,
            'request'  => $context->request,
            'response' => $response,
            ...$routeArguments,
            ...$request->getAttributes(),
        ];
    }

    /**
     * Inject route arguments to request attributes.
     *
     * @param ServerRequestInterface $request
     * @param array<string,string> $routeArguments
     * @return ServerRequestInterface
     */
    private function injectRouteArguments(
        ServerRequestInterface $request,
        array $routeArguments,
    ): ServerRequestInterface {

        $requestWithArguments = $request;

        foreach ($routeArguments as $key => $value) {
            $requestWithArguments = $requestWithArguments->withAttribute($key, $value);
        }
        return $requestWithArguments;
    }
}
