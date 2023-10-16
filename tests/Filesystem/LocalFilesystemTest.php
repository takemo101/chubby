<?php

use Tests\Filesystem\FilesystemTestCase;

describe(
    'filesystem',
    function () {
        test(
            'Verify file existence',
            function (string $filename) {
                /** @var FilesystemTestCase $this */

                $path = $this->getTestResourcePath($filename);

                $this->filesystem->write($path, 'test');

                expect($this->filesystem->exists($path))->toBeTrue();

                $this->filesystem->delete($path);
            },
        )->with([
            'test.txt',
            'test.json',
        ]);

        test(
            'Write to and read files',
            function () {
                /** @var FilesystemTestCase $this */

                $content = 'test';
                $path = $this->getTestResourcePath('test.txt');

                $this->filesystem->write($path, $content);

                expect($this->filesystem->read($path))->toEqual($content);

                $this->filesystem->delete($path);
            },
        );

        test(
            'Write data to the beginning of an existing file',
            function () {
                /** @var FilesystemTestCase $this */

                $path = $this->getTestResourcePath('test.txt');
                $content = 'content';
                $prepend = 'prepend';
                $this->filesystem->write($path, $content);

                $this->filesystem->prepend($path, $prepend);
                $this->filesystem->prepend($path, $prepend);

                expect($this->filesystem->read($path))->toEqual($prepend . $prepend . $content);

                $this->filesystem->delete($path);
            },
        );

        test(
            'Write data to the end of an existing file',
            function () {
                /** @var FilesystemTestCase $this */

                $path = $this->getTestResourcePath('test.txt');
                $content = 'content';
                $append = 'append';
                $this->filesystem->write($path, $content);

                $this->filesystem->append($path, $append);
                $this->filesystem->append($path, $append);

                expect($this->filesystem->read($path))->toEqual($content . $append . $append);

                $this->filesystem->delete($path);
            },
        );

        test(
            'Delete existing files',
            function (string $filename) {
                /** @var FilesystemTestCase $this */

                $path = $this->getTestResourcePath($filename);
                $this->filesystem->write($path, 'test');

                expect($this->filesystem->exists($path))->toBeTrue();

                $this->filesystem->delete($path);

                expect($this->filesystem->exists($path))->not->toBeTrue();
            },
        )->with([
            'test.txt',
            'test.json',
        ]);

        test(
            'Change access permissions for existing files',
            function (int $permission) {
                /** @var FilesystemTestCase $this */

                $path = $this->getTestResourcePath('test.txt');
                $this->filesystem->write($path, 'test');

                $this->filesystem->chmod($path, $permission);

                expect($this->filesystem->permission($path) & $permission)->toEqual($permission);

                $this->filesystem->delete($path);
            },
        )->with([
            0777,
            0755,
            0644,
        ])->skipOnWindows();

        test(
            'Create a copy of an existing files',
            function () {
                /** @var FilesystemTestCase $this */

                $directory = $this->getTestResourcePath('copy-directory');
                $this->filesystem->makeDirectory($directory);

                $originalPath = $this->getTestResourcePath('original-file.txt');
                $this->filesystem->write($originalPath, 'test');
                $copyPath = $directory . '/copy-file.txt';
                $this->filesystem->copy($originalPath, $copyPath);

                expect($this->filesystem->exists($copyPath))->toBeTrue();

                $this->filesystem->delete($originalPath);
                $this->filesystem->deleteDirectory($directory, false);
            },
        );

        test(
            'Move existing files',
            function () {
                /** @var FilesystemTestCase $this */

                $directory = $this->getTestResourcePath('move-directory');
                $this->filesystem->makeDirectory($directory);

                $originalPath = $this->getTestResourcePath('original-file.txt');
                $this->filesystem->write($originalPath, 'test');
                $movePath = $directory . '/move-file.txt';
                $this->filesystem->move($originalPath, $movePath);

                expect($this->filesystem->exists($movePath))->toBeTrue();
                expect($this->filesystem->exists($originalPath))->not->toBeTrue();

                $this->filesystem->deleteDirectory($directory, false);
            },
        );

        test(
            'Create a symbolic link for an existing file',
            function () {
                /** @var FilesystemTestCase $this */

                $directory = $this->getTestResourcePath('link-directory');
                $this->filesystem->makeDirectory($directory);

                $originalPath = $this->getTestResourcePath('original-file.txt');
                $this->filesystem->write($originalPath, 'test');
                $linkPath = $directory . '/link-file.text';
                $this->filesystem->symlink($originalPath, $linkPath);

                expect($this->filesystem->isLink($linkPath))->toBeTrue();

                $this->filesystem->delete($originalPath);
                $this->filesystem->deleteDirectory($directory, false);
            },
        );

        test(
            'Get list information of existing files',
            function () {
                /** @var FilesystemTestCase $this */

                $expected = [];

                foreach ([
                    'a',
                    'b',
                    'c',
                    'd',
                ] as $filename) {
                    $path = $this->getTestResourcePath($filename);
                    $this->filesystem->write($path, 'test');
                    $expected[] = $path;
                }

                $list = $this->filesystem->glob($this->getTestResourcePath('*'));

                foreach ($list as $path) {
                    $this->filesystem->delete($path);

                    expect($path)->toBeIn($expected);
                }
            },
        );

        test(
            'Create a directory and verify its existence',
            function (string $directoryName) {
                /** @var FilesystemTestCase $this */

                $directoryPath = $this->getTestResourcePath($directoryName);
                $this->filesystem->makeDirectory($directoryPath, 0777, true);

                expect($this->filesystem->isDirectory($directoryPath))->toBeTrue();

                $removeDirectoryName = explode('/', $directoryName)[0];

                $this->filesystem->deleteDirectory(
                    $this->getTestResourcePath($removeDirectoryName),
                    false,
                );
            },
        )->with([
            'a',
            'b/b',
            'c/c/c',
        ]);

        test(
            'Move an existing directory',
            function (string $directoryName) {
                /** @var FilesystemTestCase $this */

                $fromDirectoryPath = $this->getTestResourcePath($directoryName);
                $toDirectoryPath = $this->getTestResourcePath('move-directory');

                $expectedDiretoryPath = $toDirectoryPath . '/' . $directoryName;

                $this->filesystem->makeDirectory($fromDirectoryPath, 0777, true);
                $this->filesystem->makeDirectory($toDirectoryPath, 0777, true);

                $this->filesystem->moveDirectory($fromDirectoryPath, $expectedDiretoryPath);

                expect($this->filesystem->isDirectory($fromDirectoryPath))->not->toBeTrue();
                expect($this->filesystem->isDirectory($expectedDiretoryPath))->toBeTrue();

                $this->filesystem->deleteDirectory($toDirectoryPath, false);
            },
        )->with([
            'a',
            'b',
            'c',
        ]);

        test(
            'Copy an existing directory',
            function (string $directoryName) {
                /** @var FilesystemTestCase $this */

                $fromDirectoryPath = $this->getTestResourcePath($directoryName);
                $copyDirectoryPath = $this->getTestResourcePath('copy-directory');

                $toDirectoryPath = $copyDirectoryPath . '/' . $directoryName;

                $this->filesystem->makeDirectory($fromDirectoryPath, 0777, true);
                $this->filesystem->makeDirectory($copyDirectoryPath, 0777, true);

                $this->filesystem->copyDirectory($fromDirectoryPath, $toDirectoryPath);

                expect($this->filesystem->isDirectory($toDirectoryPath))->toBeTrue();

                $this->filesystem->deleteDirectory($fromDirectoryPath, false);
                $this->filesystem->deleteDirectory($copyDirectoryPath, false);
            },
        )->with([
            'a',
            'b',
            'c',
        ]);
    }
)->group('filesystem');
