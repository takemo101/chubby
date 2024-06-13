<?php

namespace Takemo101\Chubby\Http\Listener;

use Takemo101\Chubby\Event\Attribute\AsEventListener;
use Takemo101\Chubby\Http\Event\BeforeStartContext;
use Takemo101\Chubby\Http\Uri\ApplicationUri;

#[AsEventListener(BeforeStartContext::class)]
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
     * @param BeforeStartContext $event
     * @return void
     */
    public function __invoke(
        BeforeStartContext $event
    ): void {
        $request = $event->getRequest();

        $this->uri->replace($request->getUri());
    }
}
