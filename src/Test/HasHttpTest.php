<?php

namespace Takemo101\Chubby\Test;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Http\SlimHttpAdapter;
use RuntimeException;

/**
 * @method ApplicationContainer getContainer()
 *
 * @mixin TestCase|HasContainerTest
 */
trait HasHttpTest
{
    /**
     * @var SlimHttpAdapter
     */
    private SlimHttpAdapter $http;

    /**
     * Set slim http adapter.
     *
     * @return void
     */
    protected function setUpHttp(): void
    {
        $this->http = $this->getContainer()->get(SlimHttpAdapter::class);
    }

    /**
     * Get slim http adapter.
     *
     * @return SlimHttpAdapter
     */
    protected function getHttp(): SlimHttpAdapter
    {
        return isset($this->http)
            ? $this->http
            : throw new RuntimeException('Http is not set.');
    }

    /**
     * Create a request.
     *
     * @param string $method HTTP method
     * @param string|UriInterface $uri
     * @param mixed[] $serverParams server parameters
     * @return ServerRequestInterface
     */
    protected function createRequest(
        string $method,
        $uri,
        array $serverParams = [],
    ): ServerRequestInterface {

        /** @var ServerRequestFactoryInterface */
        $factory =  $this->getContainer()->get(ServerRequestFactoryInterface::class);

        return $factory->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * Create a form request.
     *
     * @param string $method The HTTP method
     * @param string|UriInterface $uri
     * @param mixed[]|null $data form data
     * @param mixed[] $serverParams server parameters
     * @return ServerRequestInterface
     */
    protected function createFormRequest(
        string $method,
        $uri,
        ?array $data = null,
        array $serverParams = [],
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri, $serverParams);

        if ($data !== null) {
            $request = $request->withParsedBody($data);
        }

        return $request->withHeader('Content-Type', 'application/x-www-form-urlencoded');
    }

    /**
     * Create a json request.
     *
     * @param string $method HTTP method
     * @param string|UriInterface $uri
     * @param mixed[]|null $data json data
     * @param mixed[] $serverParams server parameters
     *
     * @return ServerRequestInterface
     */
    protected function createJsonRequest(
        string $method,
        $uri,
        ?array $data = null,
        array $serverParams = [],
    ): ServerRequestInterface {
        $request = $this->createRequest($method, $uri, $serverParams);

        if ($data !== null) {
            $request->getBody()->write((string)json_encode($data));
        }

        return $request->withHeader('Content-Type', 'application/json');
    }

    /**
     * Create a response.
     *
     * @param int $code HTTP status code
     * @param string $reasonPhrase Reason phrase to associate with status code
     *
     * @throws RuntimeException
     *
     * @return ResponseInterface
     */
    protected function createResponse(
        int $code = 200,
        string $reasonPhrase = '',
    ): ResponseInterface {
        /** @var ResponseFactoryInterface */
        $factory = $this->getContainer()->get(ResponseFactoryInterface::class);

        return $factory->createResponse($code, $reasonPhrase);
    }

    /**
     * Handle requests with Slim.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function request(ServerRequestInterface $request): ResponseInterface
    {
        return $this->getHttp()->handle($request);
    }

    public function get(string $uri, array $serverParams = []): ResponseInterface
    {
        return $this->request(
            $this->createRequest(
                method: 'GET',
                uri: $uri,
                serverParams: $serverParams,
            ),
        );
    }

    public function post(string $uri, ?array $data = [], array $serverParams = []): ResponseInterface
    {
        return $this->request(
            $this->createFormRequest(
                method: 'POST',
                uri: $uri,
                data: $data,
                serverParams: $serverParams,
            ),
        );
    }

    public function put(
        string $uri,
        ?array $data = [],
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createFormRequest(
                method: 'PUT',
                uri: $uri,
                data: $data,
                serverParams: $serverParams,
            ),
        );
    }

    public function patch(
        string $uri,
        ?array $data = [],
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createFormRequest(
                method: 'PATCH',
                uri: $uri,
                data: $data,
                serverParams: $serverParams,
            ),
        );
    }

    public function delete(
        string $uri,
        ?array $data = [],
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createFormRequest(
                method: 'DELETE',
                uri: $uri,
                data: $data,
                serverParams: $serverParams,
            ),
        );
    }

    public function options(
        string $uri,
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createRequest(
                method: 'OPTIONS',
                uri: $uri,
                serverParams: $serverParams,
            ),
        );
    }

    public function getJson(
        string $uri,
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createJsonRequest(
                method: 'GET',
                uri: $uri,
                serverParams: $serverParams,
            ),
        );
    }

    public function postJson(
        string $uri,
        ?array $data = [],
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createJsonRequest(
                method: 'POST',
                uri: $uri,
                data: $data,
                serverParams: $serverParams,
            ),
        );
    }

    public function putJson(
        string $uri,
        ?array $data = [],
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createJsonRequest(
                method: 'PUT',
                uri: $uri,
                data: $data,
                serverParams: $serverParams,
            ),
        );
    }

    public function patchJson(
        string $uri,
        ?array $data = [],
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createJsonRequest(
                method: 'PATCH',
                uri: $uri,
                data: $data,
                serverParams: $serverParams,
            ),
        );
    }

    public function deleteJson(
        string $uri,
        ?array $data = [],
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createJsonRequest(
                method: 'DELETE',
                uri: $uri,
                data: $data,
                serverParams: $serverParams,
            ),
        );
    }

    public function optionsJson(
        string $uri,
        array $serverParams = [],
    ): ResponseInterface {
        return $this->request(
            $this->createJsonRequest(
                method: 'OPTIONS',
                uri: $uri,
                serverParams: $serverParams,
            ),
        );
    }
}
