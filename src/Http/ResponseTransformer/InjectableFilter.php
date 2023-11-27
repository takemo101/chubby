<?php

namespace Takemo101\Chubby\Http\ResponseTransformer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Takemo101\Chubby\Contract\StreamFactoryInjectable;

class InjectableFilter implements ResponseTransformer
{
    /**
     * constructor
     *
     * @param StreamFactoryInterface $streamFactory
     */
    public function __construct(
        private StreamFactoryInterface $streamFactory,
    ) {
        //
    }

    /**
     * Change the data type and convert it into a response object.
     * If not converted to a response, return null.
     *
     * @param mixed $data
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface|null
     */
    public function transform(
        mixed $data,
        ServerRequestInterface $request,
        ResponseInterface $response,
    ): ?ResponseInterface {

        if ($data instanceof StreamFactoryInjectable) {
            $data->setStreamFactory($this->streamFactory);
        }

        return null;
    }
}
