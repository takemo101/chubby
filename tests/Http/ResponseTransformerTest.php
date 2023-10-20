<?php

use Psr\Http\Message\ResponseInterface;
use Takemo101\Chubby\Contract\Arrayable;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\Renderer\RedirectRenderer;
use Takemo101\Chubby\Http\Renderer\StringRenderer;
use Takemo101\Chubby\Http\ResponseTransformer\ArrayableTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\RendererTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\ResponseTransformers;
use Takemo101\Chubby\Http\ResponseTransformer\StringableTransformer;
use Tests\AppTestCase;

describe(
    'response transformer',
    function () {
        test(
            'transform',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $transformer = new ArrayableTransformer();

                $response = $transformer->transform($data, $request, $response);

                expect($response)->toBeInstanceOf(ResponseInterface::class);
                expect($response->getBody()->__toString())->toEqual(
                    json_encode($data),
                );
                expect($response->getHeaderLine('Content-Type'))->toEqual(
                    'application/json',
                );
            },
        )->with([
            fn () => [
                'hoge' => 'fuga',
            ],
            new class () implements Arrayable, \JsonSerializable {
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
            'transform2',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $transformer = new StringableTransformer();

                $response = $transformer->transform($data, $request, $response);

                expect($response)->toBeInstanceOf(ResponseInterface::class);
                expect($response->getBody()->__toString())->toEqual((string)$data);
                expect($response->getHeaderLine('Content-Type'))->toEqual(
                    'text/plain',
                );
            },
        )->with([
            'hoge',
            new class () implements \Stringable {
                public function __toString()
                {
                    return 'hoge';
                }
            },
        ]);

        test(
            'transform3',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $transformer = new RendererTransformer();

                $response = $transformer->transform($data, $request, $response);

                expect($response)->toBeInstanceOf(ResponseInterface::class);
            },
        )->with([
            new JsonRenderer(['hoge' => 'fuga']),
            new StringRenderer('hoge'),
            new RedirectRenderer('http://example.com'),
        ]);

        test(
            'transform4',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $transformer = new ResponseTransformers(
                    new ArrayableTransformer(),
                    new StringableTransformer(),
                );

                $response = $transformer->transform($data, $request, $response);

                expect($response)->toBeInstanceOf(ResponseInterface::class);
            },
        )->with([
            fn () => [
                'hoge' => 'fuga',
            ],
            new class () implements Arrayable, \JsonSerializable {
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
            'hoge',
            new class () implements \Stringable {
                public function __toString()
                {
                    return 'hoge';
                }
            },
        ]);
    }
)->group('transformer');
