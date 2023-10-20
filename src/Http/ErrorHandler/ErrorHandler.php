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
use Throwable;

/**
 * Default Error Renderer.
 */
class ErrorHandler implements ErrorHandlerInterface
{
    /**
     * @var ErrorResponseRender[]
     */
    private array $renders;

    public function __construct(
        private ResponseFactoryInterface $responseFactory,
        private LoggerInterface $logger,
        ErrorResponseRender ...$renders
    ) {
        $this->setRender(...$renders);
    }

    /**
     * Add error response renderer.
     *
     * @param ErrorResponseRender ...$renders
     * @return static
     */
    public function addRender(ErrorResponseRender ...$renders): static
    {
        $this->renders = [
            ...$renders,
            ...$this->renders,
        ];

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
        $this->renders = empty($renders)
            ? [
                new HtmlErrorResponseRender(),
                new JsonErrorResponseRender(),
            ]
            : $renders;

        return $this;
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

        $response = $this->responseFactory->createResponse();

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
        foreach ($this->renders as $render) {
            if ($_response = $render->render(
                request: $request,
                response: $response,
                exception: $exception,
                setting: $setting
            )) {
                $response = $_response;
                break;
            }
        }

        return $response->withStatus($this->getHttpStatusCode($exception));
    }
}
