<?php

namespace Takemo101\Chubby\Http\Listener;

use Takemo101\Chubby\Event\Attribute\AsEventListener;
use Takemo101\Chubby\Http\Event\ContextCreated;
use Takemo101\Chubby\Http\Uri\ApplicationUri;

#[AsEventListener(ContextCreated::class)]
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
     * @param ContextCreated $event
     * @return void
     */
    public function __invoke(
        ContextCreated $event
    ): void {
        $request = $event->getRequest();

        $this->uri->replace($request->getUri());
    }
}
