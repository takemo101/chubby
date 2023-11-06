<?php

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Takemo101\Chubby\Http\Renderer\AttatchmentRenderer;
use Takemo101\Chubby\Http\Renderer\StaticRenderer;
use Tests\AppTestCase;

describe(
    'response renderer',
    function () {
        test(
            'Converting file data to response with StaticRenderer',
            function (mixed $file, string $expectedMimeType) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $renderer = new StaticRenderer($file);
                $renderer->setStreamFactory($this->getContainer()->get(StreamFactoryInterface::class));

                $actual = $renderer->render($request, $response);

                expect($actual->getStatusCode())->toEqual(StatusCodeInterface::STATUS_OK);
                expect($actual->getHeaderLine('Content-Type'))->toEqual($expectedMimeType);
            },
        )->with([
            [
                new \SplFileInfo(dirname(__DIR__, 1) . '/resource/asset/sample.jpeg'),
                'image/jpeg',
            ],
            [
                new \SplFileInfo(dirname(__DIR__, 1) . '/resource/asset/sample.txt'),
                'text/plain',
            ],
        ]);

        test(
            'Converting file data to download response with AttatchmentRenderer',
            function (mixed $file) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $renderer = new AttatchmentRenderer($file);
                $renderer->setStreamFactory($this->getContainer()->get(StreamFactoryInterface::class));

                $actual = $renderer->render($request, $response);

                expect($actual->getStatusCode())->toEqual(StatusCodeInterface::STATUS_OK);
                expect($actual->getHeaderLine('Content-Type'))->toEqual(AttatchmentRenderer::DefaultContentType);
                expect($actual->getHeaderLine('Content-Disposition'))->toEqual('attachment; filename="' . $file->getFilename() . '"');
            },
        )->with([
            new \SplFileInfo(dirname(__DIR__, 1) . '/resource/asset/sample.jpeg'),
            new \SplFileInfo(dirname(__DIR__, 1) . '/resource/asset/sample.txt'),
        ]);

        test(
            'Create a StaticRenderer instance from a file path',
            function (string $path) {
                /** @var AppTestCase $this */

                $renderer = StaticRenderer::fromPath($path);

                expect($renderer->getData())->toBeInstanceOf(\SplFileInfo::class);
                expect($renderer->getData()->getPathname())->toEqual($path);
            },
        )->with([
            dirname(__DIR__, 1) . '/resource/asset/sample.jpeg',
            dirname(__DIR__, 1) . '/resource/asset/sample.txt',
        ]);

        test(
            'Create a AttatchmentRenderer instance from a file path',
            function (string $path) {
                /** @var AppTestCase $this */

                $renderer = AttatchmentRenderer::fromPath($path);

                expect($renderer->getData())->toBeInstanceOf(\SplFileInfo::class);
                expect($renderer->getData()->getPathname())->toEqual($path);
            },
        )->with([
            dirname(__DIR__, 1) . '/resource/asset/sample.jpeg',
            dirname(__DIR__, 1) . '/resource/asset/sample.txt',
        ]);

        test(
            'Enable Etag in StreamRenderer',
            function () {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $renderer = StaticRenderer::fromPath(dirname(__DIR__, 1) . '/resource/asset/sample.jpeg');

                $renderer->setStreamFactory($this->getContainer()->get(StreamFactoryInterface::class));

                $renderer->enableAutoEtag();

                $actual = $renderer->render($request, $response);

                expect($actual->getStatusCode())->toEqual(StatusCodeInterface::STATUS_OK);
                expect($actual->getHeaderLine('ETag'))->not->toBeEmpty();
            },
        );


        test(
            'Enable LastModified in StreamRenderer',
            function () {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();

                $renderer = StaticRenderer::fromPath(dirname(__DIR__, 1) . '/resource/asset/sample.jpeg');

                $renderer->setStreamFactory($this->getContainer()->get(StreamFactoryInterface::class));

                $renderer->enableAutoLastModified();

                $actual = $renderer->render($request, $response);

                expect($actual->getStatusCode())->toEqual(StatusCodeInterface::STATUS_OK);
                expect($actual->getHeaderLine('Last-Modified'))->not->toBeEmpty();
            },
        );
    }
)->group('stream-renderer');
