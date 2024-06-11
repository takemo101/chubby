<?php

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Takemo101\Chubby\Http\ResponseTransformer\InjectableFilter;
use Takemo101\Chubby\Contract\ContainerInjectable;
use Takemo101\Chubby\ApplicationContainer;
use Mockery as m;

beforeEach(function () {
    $this->container = m::mock(ApplicationContainer::class);
    $this->filter = new InjectableFilter($this->container);
});

describe(
    'InjectableFilter',
    function () {

        it(
            'should set the container if the data is ContainerInjectable',
            function () {
                $data = m::mock(ContainerInjectable::class);
                $data->shouldReceive('setContainer')
                    ->once()
                    ->with($this->container);

                $request = m::mock(ServerRequestInterface::class);
                $response = m::mock(ResponseInterface::class);

                $result = $this->filter->transform($data, $request, $response);

                expect($result)->toBeNull();
            }
        );

        it(
            'should return null if the data is neither StreamFactoryInjectable nor ContainerInjectable',
            function () {
                $data = 'test';

                $request = m::mock(ServerRequestInterface::class);
                $response = m::mock(ResponseInterface::class);

                $result = $this->filter->transform($data, $request, $response);

                expect($result)->toBeNull();
            }
        );
    }
)->group('injectable-filter', 'response-transformer');
