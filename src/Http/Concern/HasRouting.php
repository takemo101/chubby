<?php

namespace Takemo101\Chubby\Http\Concern;

use Psr\Http\Message\UriInterface;
use Psr\Http\Server\MiddlewareInterface;
use Slim\Interfaces\RouteInterface;
use Slim\App as Slim;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteGroupInterface;

/**
 * @property Slim $application
 *
 * @mixin \Takemo101\Chubby\Http\HttpAdapter
 */
trait HasRouting
{
    /**
     * Add the Slim built-in routing middleware to the app middleware stack
     *
     * @param MiddlewareInterface|string|callable $middleware
     * @return self
     */
    public function add(
        MiddlewareInterface|string|callable $middleware,
    ) {
        $this->application->add($middleware);

        return $this;
    }

    /**
     * Add middleware
     *
     * @param MiddlewareInterface $middleware
     * @return self
     */
    public function addMiddleware(MiddlewareInterface $middleware): self
    {
        $this->application->addMiddleware($middleware);

        return $this;
    }

    /**
     * Get the RouteCollectorProxy's base path
     */
    public function getBasePath(): string
    {
        return $this->application->getBasePath();
    }

    /**
     * Set the RouteCollectorProxy's base path
     */
    public function setBasePath(string $basePath): RouteCollectorProxyInterface
    {
        return $this->application->setBasePath($basePath);
    }

    /**
     * Add GET route
     *
     * @param  string          $pattern  The route URI pattern
     * @param  callable|string $callable The route callback routine
     */
    public function get(string $pattern, $callable): RouteInterface
    {
        return $this->application->get($pattern, $callable);
    }

    /**
     * Add POST route
     *
     * @param  string          $pattern  The route URI pattern
     * @param  callable|string $callable The route callback routine
     */
    public function post(string $pattern, $callable): RouteInterface
    {
        return $this->application->post($pattern, $callable);
    }

    /**
     * Add PUT route
     *
     * @param  string          $pattern  The route URI pattern
     * @param  callable|string $callable The route callback routine
     */
    public function put(string $pattern, $callable): RouteInterface
    {
        return $this->application->put($pattern, $callable);
    }

    /**
     * Add PATCH route
     *
     * @param  string          $pattern  The route URI pattern
     * @param  callable|string $callable The route callback routine
     */
    public function patch(string $pattern, $callable): RouteInterface
    {
        return $this->application->patch($pattern, $callable);
    }

    /**
     * Add DELETE route
     *
     * @param  string          $pattern  The route URI pattern
     * @param  callable|string $callable The route callback routine
     */
    public function delete(string $pattern, $callable): RouteInterface
    {
        return $this->application->delete($pattern, $callable);
    }

    /**
     * Add OPTIONS route
     *
     * @param  string          $pattern  The route URI pattern
     * @param  callable|string $callable The route callback routine
     */
    public function options(string $pattern, $callable): RouteInterface
    {
        return $this->application->options($pattern, $callable);
    }

    /**
     * Add route for any HTTP method
     *
     * @param  string          $pattern  The route URI pattern
     * @param  callable|string $callable The route callback routine
     */
    public function any(string $pattern, $callable): RouteInterface
    {
        return $this->application->any($pattern, $callable);
    }

    /**
     * Add route with multiple methods
     *
     * @param  string[]        $methods  Numeric array of HTTP method names
     * @param  string          $pattern  The route URI pattern
     * @param  callable|string $callable The route callback routine
     */
    public function map(array $methods, string $pattern, $callable): RouteInterface
    {
        return $this->application->map($methods, $pattern, $callable);
    }

    /**
     * Route Groups
     *
     * This method accepts a route pattern and a callback. All route
     * declarations in the callback will be prepended by the group(s)
     * that it is in.
     * @param string|callable $callable
     */
    public function group(string $pattern, $callable): RouteGroupInterface
    {
        return $this->application->group($pattern, $callable);
    }

    /**
     * Add a route that sends an HTTP redirect
     *
     * @param string|UriInterface $to
     */
    public function redirect(string $from, $to, int $status = 302): RouteInterface
    {
        return $this->application->redirect($from, $to, $status);
    }
}
