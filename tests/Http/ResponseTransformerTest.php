<?php

use Psr\Http\Message\ResponseInterface;
use Takemo101\Chubby\Contract\Arrayable;
use Takemo101\Chubby\Contract\Renderable;
use Takemo101\Chubby\Http\Renderer\HtmlRenderer;
use Takemo101\Chubby\Http\Renderer\JsonRenderer;
use Takemo101\Chubby\Http\Renderer\RedirectRenderer;
use Takemo101\Chubby\Http\Renderer\StringRenderer;
use Takemo101\Chubby\Http\ResponseTransformer\ArrayableTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\RenderableTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\RendererTransformer;
use Takemo101\Chubby\Http\ResponseTransformer\ResponseTransformers;
use Takemo101\Chubby\Http\ResponseTransformer\StringableTransformer;
use Tests\AppTestCase;

describe(
    'response transformer',
    function () {
        test(
            'Convert array value to json response with ArrayableTransformer',
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
            'Convert string value to text response with StringableTransformer',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $transformer = new StringableTransformer();

                $response = $transformer->transform($data, $request, $response);

                expect($response)->toBeInstanceOf(ResponseInterface::class);
                expect($response->getBody()->__toString())->toEqual((string)$data);
                expect($response->getHeaderLine('Content-Type'))->toEqual(
                    StringRenderer::ContentType,
                );
            },
        )->with([
            'hoge',
            new class() implements \Stringable
            {
                public function __toString()
                {
                    return 'hoge';
                }
            },
        ]);

        test(
            'Convert object value to html response with RenderableTransformer',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $transformer = new RenderableTransformer();

                $response = $transformer->transform($data, $request, $response);

                expect($response)->toBeInstanceOf(ResponseInterface::class);
                expect($response->getBody()->__toString())->toEqual((string)$data);
                expect($response->getHeaderLine('Content-Type'))->toEqual(
                    HtmlRenderer::ContentType,
                );
            },
        )->with([
            new class() implements \Stringable, Renderable
            {
                public function __toString()
                {
                    return $this->render();
                }

                public function render(): string
                {
                    return 'hoge';
                }
            },
            new class() implements \Stringable, Renderable
            {
                public function __toString()
                {
                    return $this->render();
                }

                public function render(): string
                {
                    return 'piyo';
                }
            },
        ]);

        test(
            'Convert ResponseRenderer object to json response using RendererTransformer',
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
            new HtmlRenderer('<div>piyo</div>'),
            new RedirectRenderer('http://example.com'),
        ]);

        test(
            'Convert values ​​in various formats into responses using ResponseTransformers',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $transformer = new ResponseTransformers(
                    new ArrayableTransformer(),
                    new RenderableTransformer(),
                    new StringableTransformer(),
                );

                $response = $transformer->transform($data, $request, $response);

                expect($response)->toBeInstanceOf(ResponseInterface::class);
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
            'hoge',
            new class() implements \Stringable
            {
                public function __toString()
                {
                    return 'hoge';
                }
            },
            new class() implements \Stringable, Renderable
            {
                public function __toString()
                {
                    return $this->render();
                }

                public function render(): string
                {
                    return 'piyo';
                }
            },
        ]);
    }
)->group('transformer');
