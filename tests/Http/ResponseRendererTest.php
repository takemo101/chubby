<?php

use Fig\Http\Message\StatusCodeInterface;
use Takemo101\Chubby\Contract\Arrayable;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\Renderer\RedirectRenderer;
use Takemo101\Chubby\Http\Renderer\StringRenderer;
use Tests\AppTestCase;

describe(
    'response renderer',
    function () {
        test(
            'Convert array data to Json response with JsonRenderer',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $exceptedStatusCode = StatusCodeInterface::STATUS_ACCEPTED;

                $renderer = new JsonRenderer($data, $exceptedStatusCode);

                $actual = $renderer->render($request, $response);

                expect($actual->getStatusCode())->toEqual($exceptedStatusCode);
                expect($actual->getBody()->__toString())->toEqual(json_encode($data));
                expect($actual->getHeaderLine('Content-Type'))->toEqual('application/json');
            },
        )->with([
            fn () => [
                'hoge' => 'fuga',
            ],
            new class() implements Arrayable, \JsonSerializable
            {
                public function toArray(): array
                {
                    return [
                        'hoge' => 'fuga',
                    ];
                }

                public function jsonSerialize(): mixed
                {
                    return $this->toArray();
                }
            },
        ]);

        test(
            'Convert string data to response with StringRenderer',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $exceptedStatusCode = StatusCodeInterface::STATUS_ACCEPTED;

                $renderer = new StringRenderer($data, $exceptedStatusCode);

                $actual = $renderer->render($request, $response);

                expect($actual->getStatusCode())->toEqual($exceptedStatusCode);
                expect($actual->getBody()->__toString())->toEqual((string) $data);
                expect($actual->getHeaderLine('Content-Type'))->toEqual('text/plain');
            },
        )->with([
            'hoge',
            new class() implements \Stringable
            {
                public function __toString()
                {
                    return 'fuga';
                }
            },
        ]);

        test(
            'Set the redirect destination in RedirectRenderer and convert it to a redirect response',
            function () {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $exceptedUri = 'https://example.com';

                $renderer = new RedirectRenderer($exceptedUri);

                $actual = $renderer->render($request, $response);

                expect($actual->getStatusCode())->toEqual(StatusCodeInterface::STATUS_FOUND);
                expect($actual->getHeaderLine('Location'))->toEqual($exceptedUri);
            },
        );
    }
)->group('renderer');
