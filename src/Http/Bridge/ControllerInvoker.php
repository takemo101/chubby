<?php

namespace Takemo101\Chubby\Http\Bridge;

use Invoker\InvokerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Takemo101\Chubby\ApplicationHookTags;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Context\RequestContext;
use Takemo101\Chubby\Http\Context\RequestContextException;
use Takemo101\Chubby\Http\Event\AfterControllerExecution;
use Takemo101\Chubby\Http\Event\BeforeControllerExecution;
use Takemo101\Chubby\Http\ResponseTransformer\ResponseTransformers;
use Takemo101\Chubby\Http\Routing\DomainRouteContext;
use Takemo101\Chubby\Http\Routing\RouteArguments;
use Takemo101\Chubby\Support\ParameterKeyTypeHintResolver;

class ControllerInvoker implements InvocationStrategyInterface
{
    /**
     * constructor
     *
     * @param InvokerInterface $invoker
     * @param ResponseTransformers $transformers
     * @param Hook $hook
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(
        private InvokerInterface $invoker,
        private ResponseTransformers $transformers,
        private Hook $hook,
        private EventDispatcherInterface $dispatcher,
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

        $parameters = $this->getInjectParameters($request, $response, $routeArguments);

        /** @var mixed */
        $data = $this->invoker->call(
            $callable,
            (new ParameterKeyTypeHintResolver())->resolve(
                $callable,
                $parameters,
            ),
        );

        $transformedResponse = $this->transformers->transform(
            $data,
            $request,
            $response,
        );

        /** @var ResponseInterface */
        $hookedResponse = $this->hook->do(
            tag: ApplicationHookTags::Http_AfterControllerExecution,
            parameter: $transformedResponse,
        );

        $this->dispatcher->dispatch(
            new AfterControllerExecution($hookedResponse),
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
        // Get the domain route arguments and join them with the route arguments,
        // then set them in the request context
        $domainRouteContext = DomainRouteContext::fromRequest($request) ?? new DomainRouteContext();

        $routeArguments = $domainRouteContext->getArguments()
            ->join(new RouteArguments($routeArguments));

        /** @var ServerRequestInterface */
        $hookedRequest = $this->hook->do(
            tag: ApplicationHookTags::Http_BeforeControllerExecution,
            parameter: $this->injectRouteArguments($request, $routeArguments),
        );

        $this->dispatcher->dispatch(
            new BeforeControllerExecution(
                request: $hookedRequest,
                routeArguments: $routeArguments,
            ),
        );

        $requestContext = $this->getRequestContextOrCreate($hookedRequest);

        $requestContext
            ->setTyped($routeArguments)
            ->set(ServerRequestInterface::class, $hookedRequest)
            ->set(ResponseInterface::class, $response);

        return [
            ...$requestContext->values(),
            ...$routeArguments->arguments(),
        ];
    }

    /**
     * Inject route arguments to request attributes.
     *
     * @param ServerRequestInterface $request
     * @param RouteArguments $routeArguments
     * @return ServerRequestInterface
     */
    private function injectRouteArguments(
        ServerRequestInterface $request,
        RouteArguments $routeArguments,
    ): ServerRequestInterface {

        $requestWithArguments = $request;

        foreach ($routeArguments->arguments() as $key => $value) {
            $requestWithArguments = $requestWithArguments->withAttribute($key, $value);
        }
        return $requestWithArguments;
    }

    /**
     * Get the context from the request or create a new context.
     *
     * @param ServerRequestInterface $request
     * @return RequestContext
     * @throws RequestContextException
     */
    private function getRequestContextOrCreate(
        ServerRequestInterface $request,
    ): RequestContext {
        try {
            $context = RequestContext::fromRequest($request);
        } catch (RequestContextException $e) {

            // Check if the exception is not an instance of the error code.
            if ($e->getCode() === RequestContextException::NotInstanceOfErrorCode) {
                throw $e;
            }

            // If the context is not set, create a new context.
            $context = new RequestContext();
        }

        return $context;
    }
}
