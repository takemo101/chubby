<?php

namespace Takemo101\Chubby\Http;

use BadMethodCallException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Interfaces\RouteInterface;
use Slim\Interfaces\RouteGroupInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Takemo101\Chubby\Support\AbstractRunner;

/**
 * Execute Http processing by Slim application.
 *
 * @method Http add(MiddlewareInterface|string|callable $middleware)
 * @method Http addMiddleware(MiddlewareInterface $middleware)
 * @method string getBasePath()
 * @method RouteCollectorProxyInterface setBasePath(string $basePath)
 * @method RouteInterface get(string $pattern, callable|string $callable)
 * @method RouteInterface post(string $pattern, callable|string $callable)
 * @method RouteInterface put(string $pattern, callable|string $callable)
 * @method RouteInterface patch(string $pattern, callable|string $callable)
 * @method RouteInterface delete(string $pattern, callable|string $callable)
 * @method RouteInterface options(string $pattern, callable|string $callable)
 * @method RouteInterface any(string $pattern, callable|string $callable)
 * @method RouteInterface map(array $methods, string $pattern, callable|string $callable)
 * @method RouteGroupInterface group(string $pattern, callable|string $callable)
 * @method RouteInterface redirect(string $from, string|UriInterface $to, int $status = 302)
 */
final readonly class Http extends AbstractRunner
{
    /**
     * Create an slim instance.
     *
     * @return SlimHttpAdapter
     */
    private function getHttp(): SlimHttpAdapter
    {
        $this->getApp()->boot();

        /** @var SlimHttpAdapter */
        $slim = $this->getApp()->get(SlimHttpAdapter::class);

        return $slim;
    }

    /**
     * Run slim application
     *
     * @param ServerRequestInterface|null $request
     * @return void
     */
    public function run(?ServerRequestInterface $request = null): void
    {
        $this->getHttp()->run($request);
    }

    /**
     * Handle a request
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getHttp()->handle($request);
    }

    /**
     * Call method from SlimHttpAdapter.
     *
     * @param string $name
     * @param mixed[] $arguments
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call(string $name, array $arguments)
    {
        $http = $this->getHttp();

        if (method_exists($http, $name)) {
            return call_user_func_array(
                [$http, $name],
                $arguments,
            );
        }

        throw new BadMethodCallException("method not found: {$name}");
    }
}
