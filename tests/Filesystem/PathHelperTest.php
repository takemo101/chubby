<?php

use Takemo101\Chubby\Filesystem\PathHelper;

describe(
    'PathHelper',
    function () {

        it('can join paths', function () {
            $pathHelper = new PathHelper();

            $result = $pathHelper->join('path', 'to', 'file.txt');

            expect($result)->toBe('path/to/file.txt');
        })->skipOnWindows();

        it('can split a path', function () {
            $pathHelper = new PathHelper();

            $result = $pathHelper->split('path/to/file.txt');

            expect($result)->toBe(['path', 'to', 'file.txt']);
        })->skipOnWindows();

        it('can trim a path', function () {
            $pathHelper = new PathHelper();

            $result = $pathHelper->trim('/path/to/file.txt/');

            expect($result)->toBe('path/to/file.txt');
        })->skipOnWindows();

        it('can get the basename of a path', function () {
            $pathHelper = new PathHelper();

            $result = $pathHelper->basename('/path/to/file.txt');

            expect($result)->toBe('file.txt');
        });

        it('can get the dirname of a path', function () {
            $pathHelper = new PathHelper();

            $result = $pathHelper->dirname('/path/to/file.txt');

            expect($result)->toBe('/path/to');
        });

        it('can get the extension of a path', function () {
            $pathHelper = new PathHelper();

            $result = $pathHelper->extension('/path/to/file.txt');

            expect($result)->toBe('txt');
        });

        it('can get the filename of a path', function () {
            $pathHelper = new PathHelper();

            $result = $pathHelper->filename('/path/to/file.txt');

            expect($result)->toBe('file');
        });
    },
)->group('PathHelper', 'filesystem');
