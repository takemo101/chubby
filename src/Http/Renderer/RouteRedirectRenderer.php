<?php

namespace Takemo101\Chubby\Http\Renderer;

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteParserInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Contract\ContainerInjectable;
use LogicException;

class RouteRedirectRenderer implements ResponseRenderer, ContainerInjectable
{
    /**
     * @var ApplicationContainer|null
     */
    private ?ApplicationContainer $container = null;

    /**
     * constructor
     *
     * @param string $route
     * @param array<string,string> $data
     * @param array<string,string> $query
     * @param int $status
     * @param array<string,string> $headers
     */
    public function __construct(
        private string $route,
        private array $data = [],
        private array $query = [],
        private int $status = StatusCodeInterface::STATUS_FOUND,
        private array $headers = []
    ) {
        //
    }

    /**
     * Set url to be rendered.
     *
     * @param string $route
     * @return static
     */
    public function setRoute(string $route): static
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Set data to be rendered.
     *
     * @param array<string,string> $data
     * @return static
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set query to be rendered.
     *
     * @param array<string,string> $query
     * @return static
     */
    public function setQuery(array $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * Set status code to be rendered.
     *
     * @param int $status
     * @return static
     */
    public function setStatus(int $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Set headers to be rendered.
     *
     * @param array<string,string> $headers
     * @return static
     */
    public function setHeaders(array $headers): static
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * Set the application container implementation.
     *
     * @param ApplicationContainer $container
     * @return void
     */
    public function setContainer(ApplicationContainer $container): void
    {
        $this->container = $container;
    }

    /**
     * Get application container.
     *
     * @return ApplicationContainer
     * @throws LogicException
     */
    protected function getContainer(): ApplicationContainer
    {
        return $this->container ?? throw new LogicException('container is not set!');
    }

    /**
     * Perform response writing process.
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ResponseInterface {
        /** @var RouteParserInterface */
        $routeParser = $this->getContainer()
            ->get(RouteParserInterface::class);

        $url = $routeParser->urlFor(
            $this->route,
            $this->data,
            $this->query
        );

        return (new RedirectRenderer($url))
            ->setStatus($this->status)
            ->setHeaders($this->headers)
            ->render($request, $response);
    }
}
