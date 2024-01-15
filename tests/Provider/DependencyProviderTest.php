<?php

use Takemo101\Chubby\Bootstrap\Provider\DependencyProvider;
use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Filesystem\LocalFilesystem;
use Takemo101\Chubby\Support\ApplicationPath;
use Mockery as m;

beforeEach(function () {
    $this->path = new ApplicationPath('path/to/base');
    $this->filesystem = m::mock(LocalFilesystem::class);

    $this->provider = new DependencyProvider($this->path, $this->filesystem);
});

it('should register dependency definitions', function () {
    $definitions = m::spy(Definitions::class);

    $dependencyPath1 = 'path/to/dependency1.php';
    $dependencyPath2 = 'path/to/dependency2.php';

    $this->filesystem->shouldReceive('exists')
        ->with($dependencyPath1)
        ->andReturn(true);
    $this->filesystem->shouldReceive('require')
        ->with($dependencyPath1)
        ->andReturn(['key1' => 'value1']);
    $this->filesystem->shouldReceive('exists')
        ->with($dependencyPath2)
        ->andReturn(true);
    $this->filesystem->shouldReceive('require')
        ->with($dependencyPath2)
        ->andReturn(['key2' => 'value2']);

    $this->provider->setDependencyPath($dependencyPath1, $dependencyPath2);

    expect($this->provider->getDependencyPaths())
        ->toBe([$dependencyPath1, $dependencyPath2]);

    $this->provider->register($definitions);

    $definitions->shouldHaveReceived('add')
        ->once()
        ->with([
            'key1' => 'value1',
            'key2' => 'value2',
        ]);
})->group('DependencyProvider', 'provider');
