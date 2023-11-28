<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use DomainException;
use InvalidArgumentException;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Slim\Exception\HttpException;
use Slim\Interfaces\ErrorHandlerInterface;
use Takemo101\Chubby\Http\Renderer\ResponseRenderer;
use Takemo101\Chubby\Http\ResponseTransformer\ResponseTransformers;
use Throwable;

/**
 * Default Error Renderer.
 */
class ErrorHandler implements ErrorHandlerInterface
{
    public const IgnornReportExceptions = [
        InterruptRender::class,
        ResponseRenderer::class,
    ];

    /**
     * constructor
     *
     * @param ResponseFactoryInterface $responseFactory
     * @param LoggerInterface $logger
     * @param ErrorResponseRenders $renders
     * @param ResponseTransformers $transformers
     */
    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface $logger,
        private ErrorResponseRenders $renders,
        private ResponseTransformers $transformers,
    ) {
        //
    }

    /**
     * Add error response renderer.
     *
     * @param ErrorResponseRender ...$renders
     * @return static
     */
    public function addRender(ErrorResponseRender ...$renders): static
    {
        $this->renders->addRender(...$renders);

        return $this;
    }

    /**
     * Set error response renderer.
     *
     * @param ErrorResponseRender ...$renders
     * @return static
     */
    public function setRender(ErrorResponseRender ...$renders): static
    {
        $this->renders->setRender(...$renders);

        return $this;
    }

    /**
     * Get error response renderer.
     *
     * @template T of ErrorResponseRender
     *
     * @param class-string<T> $class
     * @return T|null
     */
    public function getRender(string $class): ?ErrorResponseRender
    {
        /** @var T|null */
        $render = $this->renders->getRender($class);

        return $render;
    }

    /**
     * Invoke.
     *
     * @param ServerRequestInterface $request The request
     * @param Throwable $exception The exception
     * @param bool $displayErrorDetails Show error details
     * @param bool $logErrors Log errors
     * @param bool $logErrorDetails Log error details
     *
     * @return ResponseInterface The response
     */
    public function __invoke(
        ServerRequestInterface $request,
        Throwable $exception,
        bool $displayErrorDetails,
        bool $logErrors,
        bool $logErrorDetails
    ): ResponseInterface {

        $errorSetting = new ErrorSetting(
            $displayErrorDetails,
            $logErrors,
            $logErrorDetails
        );

        // Report error
        if ($errorSetting->logErrors) {
            $this->report(
                request: $request,
                exception: $exception
            );
        }

        $response = $this->createResponse();

        // Render error
        return $this->render(
            request: $request,
            response: $response,
            exception: $exception,
            setting: $errorSetting,
        );
    }

    /**
     * Get http status code.
     *
     * @param Throwable $exception The exception
     *
     * @return int The http code
     */
    protected function getHttpStatusCode(Throwable $exception): int
    {
        // Detect status code
        $statusCode = StatusCodeInterface::STATUS_INTERNAL_SERVER_ERROR;

        if ($exception instanceof HttpException) {
            $statusCode = $exception->getCode();
        }

        if ($exception instanceof DomainException || $exception instanceof InvalidArgumentException) {
            // Bad request
            $statusCode = StatusCodeInterface::STATUS_BAD_REQUEST;
        }

        return $statusCode;
    }

    /**
     * Create new response.
     *
     * @return ResponseInterface The response
     */
    protected function createResponse(): ResponseInterface
    {
        return $this->responseFactory->createResponse();
    }

    /**
     * Output log.
     *
     * @param ServerRequestInterface $request
     * @param Throwable $exception
     * @return void
     */
    protected function report(
        ServerRequestInterface $request,
        Throwable $exception,
    ): void {
        foreach (self::IgnornReportExceptions as $ignornReportException) {
            if ($exception instanceof $ignornReportException) {
                return;
            }
        }

        $this->logger->error(
            $exception->getMessage(),
            [
                'exception' => $exception,
                'method' => $request->getMethod(),
                'url' => (string) $request->getUri(),
            ],
        );
    }

    /**
     * Output error response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Throwable $exception
     * @param ErrorSetting $setting
     * @return ResponseInterface
     */
    protected function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Throwable $exception,
        ErrorSetting $setting,
    ): ResponseInterface {

        if ($exception instanceof InterruptRender) {
            return $this->transformers->transform(
                data: $exception->getRenderer(),
                request: $request,
                response: $response,
            );
        }

        if ($exception instanceof ResponseRenderer) {
            return $exception->render(
                request: $request,
                response: $response,
            );
        }

        $response = $this->renders->render(
            request: $request,
            response: $response,
            exception: $exception,
            setting: $setting,
        );

        return $response->getStatusCode() !== StatusCodeInterface::STATUS_OK
            ? $response
            : $response->withStatus($this->getHttpStatusCode($exception));
    }
}
