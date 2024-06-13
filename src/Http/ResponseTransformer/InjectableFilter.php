<?php

namespace Takemo101\Chubby\Http\ResponseTransformer;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Contract\ContainerInjectable;

class InjectableFilter implements ResponseTransformer
{
    /**
     * constructor
     *
     * @param ApplicationContainer $container
     */
    public function __construct(
        private ApplicationContainer $container,
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

        if ($data instanceof ContainerInjectable) {
            $data->setContainer($this->container);
        }

        return null;
    }
}
