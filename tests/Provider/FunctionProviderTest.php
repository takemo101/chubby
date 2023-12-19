<?php

use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Support\ApplicationPath;
use Takemo101\Chubby\Bootstrap\Provider\FunctionProvider;
use Mockery as m;

beforeEach(function () {
    $this->path = new ApplicationPath('path/to/base');
    $this->filesystem = m::mock(LocalFilesystem::class);

    $this->provider = new FunctionProvider($this->path, $this->filesystem);
});

it('should set the function path', function () {
    $path = 'path/to/dependency.php';

    $this->provider->setFunctionPath($path);

    expect($this->provider->getFunctionPath())->toBe($path);
})->group('FunctionProvider', 'provider');
