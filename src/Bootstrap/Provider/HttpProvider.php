<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use Slim\App as Slim;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Log\LoggerInterface;
use Slim\CallableResolver;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\InvocationStrategyInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Middleware\BodyParsingMiddleware;
use Slim\Middleware\ErrorMiddleware;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Bootstrap\Support\ConfigBasedDefinitionReplacer;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Event\EventRegister;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Bridge\ControllerInvoker;
use Takemo101\Chubby\Http\Configurer\DefaultSlimConfigurer;
use Takemo101\Chubby\Http\Configurer\SlimConfigurer;
use Takemo101\Chubby\Http\Factory\DefaultSlimFactory;
use Takemo101\Chubby\Http\Factory\SlimFactory;
use Takemo101\Chubby\Http\ErrorHandler\ErrorHandler;
use Takemo101\Chubby\Http\ErrorHandler\ErrorResponseRenders;
use Takemo101\Chubby\Http\GlobalMiddlewareCollection;
use Takemo101\Chubby\Http\Listener\ApplicationUriReplace;
use Takemo101\Chubby\Http\Middleware\StartContext;
use Takemo101\Chubby\Http\ResponseTransformer\ArrayableTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\InjectableFilter;
use Takemo101\Chubby\Http\ResponseTransformer\RenderableTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\RendererTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\ResponseTransformers;
use Takemo101\Chubby\Http\ResponseTransformer\StringableTransformer;
use Takemo101\Chubby\Http\SlimHttp;

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
                SlimFactory::class => new ConfigBasedDefinitionReplacer(
                    defaultClass: DefaultSlimFactory::class,
                    configKey: 'slim.factory',
                ),
                SlimConfigurer::class => new ConfigBasedDefinitionReplacer(
                    defaultClass: DefaultSlimConfigurer::class,
                    configKey: 'slim.configurer',
                ),
                Slim::class => function (
                    SlimFactory $factory,
                    Hook $hook,
                ): Slim {
                    $slim = $factory->create();

                    $slim->add(StartContext::class);

                    $hook->doTyped($slim);
                    $hook->do(RouteCollectorProxyInterface::class, $slim);

                    return $slim;
                },
                RouteCollectorProxyInterface::class => get(Slim::class),
                SlimHttp::class => function (
                    Slim $slim,
                    SlimConfigurer $configurer,
                    EventDispatcherInterface $dispatcher,
                    Hook $hook,
                ): SlimHttp {
                    $adapter = new SlimHttp(
                        application: $slim,
                        configurer: $configurer,
                        dispatcher: $dispatcher,
                    );

                    $hook->doTyped($adapter);

                    return $adapter;
                },
                ResponseTransformers::class => function (
                    InjectableFilter $filter,
                    Hook $hook,
                ) {
                    $transformers = new ResponseTransformers(
                        $filter,
                        new RendererTransformer(),
                        new ArrayableTransformer(),
                        new RenderableTransformer(),
                        new StringableTransformer(),
                    );

                    $hook->doTyped($transformers);

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
                ErrorResponseRenders::class => function (
                    Hook $hook,
                ) {
                    $renders = new ErrorResponseRenders();

                    $hook->doTyped($renders);

                    return $renders;
                },
                ErrorHandlerInterface::class => new ConfigBasedDefinitionReplacer(
                    configKey: 'slim.error.handler',
                    defaultClass: ErrorHandler::class,
                ),
                ErrorHandler::class => function (
                    Slim $slim,
                    LoggerInterface $logger,
                    ErrorResponseRenders $renders,
                    ResponseTransformers $transformers,
                    Hook $hook,
                ) {
                    $errorHandler = new ErrorHandler(
                        $slim->getResponseFactory(),
                        $logger,
                        $renders,
                        $transformers,
                    );

                    $hook->doTyped($errorHandler);

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
                    $displayErrorDetails = $config->get('slim.error.setting.display_error_details', true);
                    /** @var boolean */
                    $logErrors = $config->get('slim.error.setting.log_errors', true);
                    /** @var boolean */
                    $logErrorDetails = $config->get('slim.error.setting.log_error_details', true);

                    $errorMiddleware = new ErrorMiddleware(
                        $slim->getCallableResolver(),
                        $slim->getResponseFactory(),
                        $displayErrorDetails,
                        $logErrors,
                        $logErrorDetails,
                        $logger
                    );

                    $errorMiddleware->setDefaultErrorHandler($errorHandler);

                    $hook->doTyped($errorMiddleware);

                    return $errorMiddleware;
                },
                BodyParsingMiddleware::class => function (
                    Hook $hook,
                ) {
                    $middleware = new BodyParsingMiddleware();

                    $hook->doTyped($middleware);

                    return $middleware;
                },
                GlobalMiddlewareCollection::class => function (
                    ConfigRepository $config,
                    Hook $hook,
                ) {
                    /** @var class-string<MiddlewareInterface>[] */
                    $classes = $config->get('slim.middlewares', []);

                    $middlewares = new GlobalMiddlewareCollection(...$classes);

                    $hook->doTyped($middlewares);

                    return $middlewares;
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
        /** @var Hook */
        $hook = $container->get(Hook::class);

        $hook->onTyped(
            fn (EventRegister $register) => $register->on(
                ApplicationUriReplace::class,
            ),
        );
    }
}
