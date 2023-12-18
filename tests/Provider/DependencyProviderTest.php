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

it('should set the dependency path', function () {
    $path = 'path/to/dependency.php';

    $this->provider->setDependencyPath($path);

    expect($this->provider->getDependencyPath())->toBe($path);
})->group('DependencyProvider', 'provider');
