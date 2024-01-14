<?php

use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Takemo101\Chubby\Http\Renderer\AttatchmentRenderer;
use Tests\AppTestCase;

describe(
    'AttatchmentRenderer',
    function () {

        it('should configure the response with the correct Content-Disposition header', function () {
            /** @var AppTestCase $this */

            $data = 'test data';
            $name = 'test.txt';
            $mime = 'text/plain';
            $status = StatusCodeInterface::STATUS_OK;
            $headers = [];

            $renderer = new AttatchmentRenderer($data, $name, $mime, null, $status, $headers);
            $renderer->setContainer($this->getContainer());

            $response = $renderer->render(
                $this->createRequest(),
                $this->createResponse(),
            );

            expect($response)->toBeInstanceOf(ResponseInterface::class);
            expect($response->getHeaderLine('Content-Disposition'))->toBe('attachment; filename="test.txt"');
        });

        it('should set the default file name if no name is provided', function () {
            /** @var AppTestCase $this */

            $data = 'test data';
            $mime = 'text/plain';
            $status = StatusCodeInterface::STATUS_OK;
            $headers = [];

            $renderer = new AttatchmentRenderer($data, '', $mime, null, $status, $headers);
            $renderer->setContainer($this->getContainer());

            $response = $renderer->render(
                $this->createRequest(),
                $this->createResponse(),
            );

            expect($response)->toBeInstanceOf(ResponseInterface::class);
            expect($response->getHeaderLine('Content-Disposition'))->toBe('attachment; filename="file"');
        });

        it('should call the finally callback when the renderer is destroyed', function () {
            $data = 'test data';
            $mime = 'text/plain';
            $status = StatusCodeInterface::STATUS_OK;
            $headers = [];
            $finallyCalled = false;

            $finally = function ($renderer) use (&$finallyCalled) {
                $finallyCalled = true;
            };

            $renderer = new AttatchmentRenderer($data, '', $mime, $finally, $status, $headers);

            unset($renderer);

            expect($finallyCalled)->toBeTrue();
        });

        it('should create a renderer instance from a file path', function () {
            $path = '/path/to/file.txt';
            $name = 'file.txt';
            $mime = 'text/plain';
            $status = StatusCodeInterface::STATUS_OK;
            $headers = [];

            $renderer = AttatchmentRenderer::fromPath($path, $name, $mime, null, $status, $headers);

            expect($renderer)->toBeInstanceOf(AttatchmentRenderer::class);
            expect($renderer->getData())->toBeInstanceOf(SplFileInfo::class);
            expect($renderer->getName())->toBe('file.txt');
            expect($renderer->getMimeType())->toBe('text/plain');
            expect($renderer->getStatus())->toBe(StatusCodeInterface::STATUS_OK);
            expect($renderer->getHeaders())->toBe($headers);
        });
    }
)->group('AttatchmentRenderer', 'renderer');
