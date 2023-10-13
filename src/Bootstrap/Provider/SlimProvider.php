<?php

namespace Takemo101\Chubby\Bootstrap\Provider;

use DI\Bridge\Slim\Bridge;
use Slim\App as Slim;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Log\LoggerInterface;
use Selective\BasePath\BasePathMiddleware;
use Slim\Interfaces\ErrorHandlerInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Middleware\ErrorMiddleware;
use Takemo101\Chubby\Application;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Config\ConfigRepository;
use Takemo101\Chubby\Hook\Hook;
use Takemo101\Chubby\Http\Bridge\ControllerInvoker;
use Takemo101\Chubby\Http\ErrorHandler\ErrorHandler;
use Takemo101\Chubby\Http\Translator\RendererTranslator;
use Takemo101\Chubby\Http\Translator\ResponseTranslators;

use function DI\get;
use function DI\create;

/**
 * Slim application related.
 */
class SlimProvider implements Provider
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
                Slim::class => function (
                    ContainerInterface $container,
                    Hook $hook,
                ): Slim {
                    $slim = Bridge::create($container);

                    $invoker = $slim->getRouteCollector()
                        ->getDefaultInvocationStrategy();

                    $slim->getRouteCollector()
                        ->setDefaultInvocationStrategy(
                            new ControllerInvoker(
                                invoker: $invoker,
                                hook: $hook,
                            ),
                        );

                    $hook->doActionByObject($slim);

                    return $slim;
                },
                ResponseTranslators::class => fn () => new ResponseTranslators(
                    new RendererTranslator(),
                ),
                Psr17Factory::class => create(Psr17Factory::class),
                ResponseFactoryInterface::class => get(Psr17Factory::class),
                ServerRequestFactoryInterface::class => get(Psr17Factory::class),
                StreamFactoryInterface::class => get(Psr17Factory::class),
                UploadedFileFactoryInterface::class => get(Psr17Factory::class),
                UriFactoryInterface::class => get(Psr17Factory::class),
                RouteParserInterface::class => fn (Slim $slim) => $slim
                    ->getRouteCollector()
                    ->getRouteParser(),
                BasePathMiddleware::class => fn (Slim $slim) => new BasePathMiddleware($slim),
                ErrorHandlerInterface::class => function (
                    Slim $slim,
                    LoggerInterface $logger,
                ) {
                    return new ErrorHandler(
                        $slim->getResponseFactory(),
                        $logger,
                    );
                },
                ErrorMiddleware::class => function (
                    Slim $slim,
                    LoggerInterface $logger,
                    ErrorHandlerInterface $errorHandler,
                    ConfigRepository $config
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

                    return $errorMiddleware;
                },
            ],
        );
    }

    /**
     * Execute Bootstrap booting process.
     *
     * @param Application $app
     * @return void
     */
    public function boot(Application $app): void
    {
        //
    }
}
