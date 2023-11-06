<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Slim\App as Slim;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\CallableResolver;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Middleware\ErrorMiddleware;
use Slim\MiddlewareDispatcher;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Bridge\ControllerInvoker;
use Takemo101\Chubby\Http\Configurer\DefaultSlimConfigurer;
use Takemo101\Chubby\Http\Configurer\SlimConfigurer;
use Takemo101\Chubby\Http\DomainRouter;
use Takemo101\Chubby\Http\Factory\DefaultSlimFactory;
use Takemo101\Chubby\Http\Factory\SlimFactory;
use Takemo101\Chubby\Http\ErrorHandler\ErrorHandler;
use Takemo101\Chubby\Http\Factory\ConfiguredSlimFactory;
use Takemo101\Chubby\Http\ResponseTransformer\ArrayableTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\InjectableFilter;
use Takemo101\Chubby\Http\ResponseTransformer\RenderableTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\RendererTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\ResponseTransformers;
use Takemo101\Chubby\Http\ResponseTransformer\StringableTransformer;
use Takemo101\Chubby\Http\Routing\DomainRouteCollector;
use Takemo101\Chubby\Http\Routing\DomainRouteHandler;
use Takemo101\Chubby\Http\SlimHttpAdapter;

use function DI\get;
use function DI\create;

/**
 * Slim application related.
 */
class HttpProvider implements Provider
{
    /**
     * @var string Provider name.
     */
    public const ProviderName = 'slim';

    /**
     * Execute Bootstrap providing process.
     *
     * @param Definitions $definitions
     * @return void
     */
    public function register(Definitions $definitions): void
    {
        $definitions->add(
            [
                InvocationStrategyInterface::class => get(ControllerInvoker::class),
                SlimFactory::class => get(DefaultSlimFactory::class),
                SlimConfigurer::class => get(DefaultSlimConfigurer::class),
                Slim::class => function (
                    ConfiguredSlimFactory $factory,
                    Hook $hook,
                ): Slim {
                    $slim = $factory->create();

                    $hook->doByObject($slim);

                    return $slim;
                },
                SlimHttpAdapter::class => function (
                    Slim $slim,
                    Hook $hook,
                ): SlimHttpAdapter {
                    $adapter = new SlimHttpAdapter($slim);

                    $hook->doByObject($adapter);

                    return $adapter;
                },
                ResponseTransformers::class => function (
                    InjectableFilter $injectableFilter,
                    Hook $hook,
                ) {
                    $transformers = new ResponseTransformers(
                        $injectableFilter,
                        new RendererTransformer(),
                        new ArrayableTransformer(),
                        new RenderableTransformer(),
                        new StringableTransformer(),
                    );

                    $hook->doByObject($transformers);

                    return $transformers;
                },
                Psr17Factory::class => create(Psr17Factory::class),
                ResponseFactoryInterface::class => get(Psr17Factory::class),
                ServerRequestFactoryInterface::class => get(Psr17Factory::class),
                StreamFactoryInterface::class => get(Psr17Factory::class),
                UploadedFileFactoryInterface::class => get(Psr17Factory::class),
                UriFactoryInterface::class => get(Psr17Factory::class),
                RouteParserInterface::class => fn (Slim $slim) => $slim
                    ->getRouteCollector()
                    ->getRouteParser(),
                CallableResolverInterface::class => fn (
                    ApplicationContainer $container,
                ) => new CallableResolver($container),
                ErrorHandlerInterface::class => get(ErrorHandler::class),
                ErrorHandler::class => function (
                    Slim $slim,
                    LoggerInterface $logger,
                    Hook $hook,
                ) {
                    $errorHandler = new ErrorHandler(
                        $slim->getResponseFactory(),
                        $logger,
                    );

                    $hook->doByObject($errorHandler);

                    return $errorHandler;
                },
                ErrorMiddleware::class => function (
                    Slim $slim,
                    LoggerInterface $logger,
                    ErrorHandlerInterface $errorHandler,
                    ConfigRepository $config,
                    Hook $hook,
                ) {
                    /** @var boolean */
                    $displayErrorDetails = $config->get('display_error_details', true);
                    /** @var boolean */
                    $logErrors = $config->get('log_errors', true);
                    /** @var boolean */
                    $logErrorDetails = $config->get('log_error_details', true);

                    $errorMiddleware = new ErrorMiddleware(
                        $slim->getCallableResolver(),
                        $slim->getResponseFactory(),
                        $displayErrorDetails,
                        $logErrors,
                        $logErrorDetails,
                        $logger
                    );

                    $errorMiddleware->setDefaultErrorHandler($errorHandler);

                    $hook->doByObject($errorMiddleware);

                    return $errorMiddleware;
                },
                DomainRouteCollector::class => function (
                    Hook $hook,
                ) {
                    $routeCollector = new DomainRouteCollector();

                    $hook->doByObject($routeCollector);

                    return $routeCollector;
                },
                DomainRouter::class => function (
                    DomainRouteCollector $routeCollector,
                    DomainRouteHandler $routeHandler,
                    CallableResolverInterface $callableResolver,
                    ApplicationContainer $container,
                    Hook $hook,
                ) {
                    $router = new DomainRouter(
                        new MiddlewareDispatcher(
                            kernel: $routeHandler,
                            callableResolver: $callableResolver,
                            container: $container,
                        ),
                        $routeCollector,
                    );

                    $router->add(ErrorMiddleware::class);

                    $hook->doByObject($router);

                    return $router;
                }
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function boot(ApplicationContainer $container): void
    {
        //
    }
}
