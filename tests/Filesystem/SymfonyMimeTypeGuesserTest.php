<?php

use Takemo101\Chubby\Filesystem\Mime\SymfonyMimeTypeGuesser;
use Symfony\Component\Mime\MimeTypesInterface;
use Symfony\Component\Mime\MimeTypes;
use Mockery as m;

describe(
    'SymfonyMimeTypeGuesser',
    function () {

        beforeEach(function () {
            $this->mimeTypes = m::mock(MimeTypesInterface::class);
            $this->guesser = new SymfonyMimeTypeGuesser($this->mimeTypes);
        });

        it('should return the guessed MIME type for a string path', function () {
            $path = '/path/to/file.txt';
            $expectedMimeType = 'text/plain';

            $this->mimeTypes->shouldReceive('getMimeTypes')
                ->with('txt')
                ->andReturn([]);

            $this->mimeTypes->shouldReceive('guessMimeType')
                ->with($path)
                ->andReturn($expectedMimeType);

            $result = $this->guesser->guess($path);

            expect($result)->toBe($expectedMimeType);
        });

        it('should return the guessed MIME type for a SplFileInfo object', function () {
            $path = '/path/to/file.txt';
            $fileInfo = new SplFileInfo($path);
            $expectedMimeType = 'text/plain';

            $this->mimeTypes->shouldReceive('getMimeTypes')
                ->with('txt')
                ->andReturn([]);

            $this->mimeTypes->shouldReceive('guessMimeType')
                ->with($path)
                ->andReturn($expectedMimeType);

            $result = $this->guesser->guess($fileInfo);

            expect($result)->toBe($expectedMimeType);
        });

        it('should throw an exception for invalid argument', function () {
            $invalidArgument = 123;

            expect(fn () => $this->guesser->guess($invalidArgument))
                ->toThrow(InvalidArgumentException::class);
        });
    }
)->group('SymfonyMimeTypeGuesser', 'mime');
