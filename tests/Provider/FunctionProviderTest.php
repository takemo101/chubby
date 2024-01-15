<?php

use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Support\ApplicationPath;
use Takemo101\Chubby\Bootstrap\Provider\FunctionProvider;
use Mockery as m;
use Takemo101\Chubby\ApplicationContainer;

beforeEach(function () {
    $this->path = new ApplicationPath('path/to/base');
    $this->filesystem = m::mock(LocalFilesystem::class);

    $this->provider = new FunctionProvider($this->path, $this->filesystem);
});

it('should load function files', function () {

    $container = m::mock(ApplicationContainer::class);

    $functionPath1 = 'path/to/function1.php';
    $functionPath2 = 'path/to/function2.php';

    $this->filesystem->shouldReceive('exists')
        ->with($functionPath1)
        ->andReturn(true);
    $this->filesystem->shouldReceive('require')
        ->with($functionPath1);
    $this->filesystem->shouldReceive('exists')
        ->with($functionPath2)
        ->andReturn(true);
    $this->filesystem->shouldReceive('require')
        ->with($functionPath2);

    $this->provider->setFunctionPath($functionPath1, $functionPath2);

    expect($this->provider->getFunctionPaths())
        ->toBe([$functionPath1, $functionPath2]);

    $this->provider->boot($container);
})->group('FunctionProvider', 'provider');
