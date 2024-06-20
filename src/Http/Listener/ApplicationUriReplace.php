<?php

namespace Takemo101\Chubby\Http\Listener;

use Takemo101\Chubby\Event\Attribute\AsEventListener;
use Takemo101\Chubby\Http\Event\RequestReceived;
use Takemo101\Chubby\Http\Uri\ApplicationUri;

#[AsEventListener(RequestReceived::class)]
class ApplicationUriReplace
{
    /**
     * constructor
     *
     * @param ApplicationUri $uri
     */
    public function __construct(
        private ApplicationUri $uri,
    ) {
        //
    }

    /**
     * @param RequestReceived $event
     * @return void
     */
    public function __invoke(
        RequestReceived $event
    ): void {
        $request = $event->getRequest();

        $this->uri->replace($request->getUri());
    }
}
