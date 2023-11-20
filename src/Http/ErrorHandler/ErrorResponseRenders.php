<?php

namespace Takemo101\Chubby\Http\ErrorHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ErrorResponseRenders
{
    /**
     * @var array<class-string<ErrorResponseRender>,ErrorResponseRender>
     */
    private array $renders;

    /**
     * @var class-string<ErrorResponseRender>[]
     */
    private array $order = [];

    /**
     * constructor
     *
     * @param ErrorResponseRender ...$renders
     */
    public function __construct(
        ErrorResponseRender ...$renders,
    ) {
        $this->setRender(...$renders);
    }

    /**
     * Add ErrorResponseRender.
     *
     * @param ErrorResponseRender ...$renders
     * @return self
     */
    public function addRender(ErrorResponseRender ...$renders): self
    {
        foreach ($renders as $render) {
            $class = get_class($render);

            if (isset($this->renders[$class])) {
                continue;
            }

            $this->renders[$class] = $render;
            $this->order[] = $class;
        }

        return $this;
    }

    /**
     * Set ErrorResponseRender.
     *
     * @param ErrorResponseRender ...$renders
     * @return static
     */
    public function setRender(ErrorResponseRender ...$renders): static
    {
        $order = [];

        $renders = empty($renders)
            ? [
                new JsonErrorResponseRender(),
                new HtmlErrorResponseRender(),
            ]
            : $renders;

        /** @var array<class-string<ErrorResponseRender>,ErrorResponseRender> */
        $classes = [];

        foreach ($renders as $render) {
            $class = get_class($render);

            if (isset($classes[$class])) {
                continue;
            }

            $classes[$class] = $render;
            $order[] = $class;
        }

        $this->renders = $classes;
        $this->order = $order;

        return $this;
    }

    /**
     * Get ErrorResponseRender.
     *
     * @template T of ErrorResponseRender
     *
     * @param class-string<T> $class
     * @return T|null
     */
    public function getRender(string $class): ?ErrorResponseRender
    {
        /** @var T|null */
        $render = $this->renders[$class] ?? null;

        return $render;
    }

    /**
     * Perform response writing process.
     * Returns null if there is no response.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param Throwable $exception
     * @param ErrorSetting $setting
     *
     * @return ResponseInterface
     */
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
        Throwable $exception,
        ErrorSetting $setting,
    ): ResponseInterface {
        $order = array_reverse($this->order);

        foreach ($order as $class) {

            /** @var ErrorResponseRender */
            $render = $this->renders[$class];

            if (
                $output = $render->render(
                    $request,
                    $response,
                    $exception,
                    $setting,
                )
            ) {
                return $output;
            }
        }

        return $response;
    }
}
