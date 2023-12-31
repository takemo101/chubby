<?php

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\EventDispatcher\ListenerProviderInterface;
use Psr\EventDispatcher\StoppableEventInterface;
use Takemo101\Chubby\Event\EventDispatcher;
use Mockery as m;

describe(
    'EventDispatcher',
    function () {

        beforeEach(function () {
            $this->provider = m::mock(ListenerProviderInterface::class);
            $this->dispatcher = new EventDispatcher($this->provider);
        });

        it('should dispatch event to listeners', function () {
            $event = new stdClass();

            $listener1 = m::mock(TestEventListener::class);
            $listener2 = m::mock(TestEventListener::class);

            $this->provider->shouldReceive('getListenersForEvent')
                ->once()
                ->with($event)
                ->andReturn([$listener1, $listener2]);

            $listener1->shouldReceive('__invoke')
                ->once()
                ->with($event);

            $listener2->shouldReceive('__invoke')
                ->once()
                ->with($event);

            $result = $this->dispatcher->dispatch($event);

            expect($result)->toBe($event);
        });

        it('should stop propagation if event is stoppable', function () {
            $event = m::mock(StoppableEventInterface::class);

            $listener1 = m::mock(TestEventListener::class);
            $listener2 = m::mock(TestEventListener::class);

            $this->provider->shouldReceive('getListenersForEvent')
                ->once()
                ->with($event)
                ->andReturn([$listener1, $listener2]);

            $listener1->shouldReceive('__invoke')
                ->once()
                ->with($event);

            $event->shouldReceive('isPropagationStopped')
                ->once()
                ->andReturn(true);

            $listener2->shouldNotReceive('__invoke');

            $result = $this->dispatcher->dispatch($event);

            expect($result)->toBe($event);
        });
    }
)->group('EventDispatcher', 'event');
