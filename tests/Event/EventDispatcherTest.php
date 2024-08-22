<?php

use Takemo101\Chubby\Event\EventDispatcher;
use Takemo101\Chubby\Event\StoppableEvent;
use Takemo101\Chubby\Event\ListenerProvider;
use Mockery as m;
use Takemo101\Chubby\Event\Exception\EventListenerHandlingExceptions;

describe(
    'EventDispatcher',
    function () {
        beforeEach(function () {
            $this->provider = m::mock(ListenerProvider::class);
            $this->dispatcher = new EventDispatcher($this->provider);
        });

        it('should dispatch event to listeners and return the event object', function () {
            $event = new stdClass();

            $listener1 = new class {
                public function __invoke(stdClass $event)
                {
                    //
                }
            };
            $listener2 = new class {
                public function __invoke(stdClass $event)
                {
                    //
                }
            };

            $this->provider->shouldReceive('getListenersForEvent')
                ->once()
                ->with($event)
                ->andReturn([$listener1, $listener2]);

            $actual = $this->dispatcher->dispatch($event);

            expect($actual)->toBe($event);
        });

        it('should dispatch event to listeners with event name and return the event object', function () {
            $event = new stdClass();
            $listener1 = new class {
                public function __invoke(stdClass $event)
                {
                    //
                }
            };
            $listener2 = new class {
                public function __invoke(stdClass $event)
                {
                    //
                }
            };
            $this->provider->shouldReceive('getListeners')
                ->once()
                ->with('event.name')
                ->andReturn([$listener1, $listener2]);

            $actual = $this->dispatcher->dispatch($event, 'event.name');
            expect($actual)->toBe($event);
        });

        it('should stop propagation if event is stoppable and return the event object', function () {
            $event = new class() extends StoppableEvent {
                //
            };

            $listener1 = new class {
                public function __invoke(StoppableEvent $event)
                {
                    $event->stopPropagation();
                }
            };
            $listener2 = m::mock(EventDispatcherTestListener::class);

            $this->provider->shouldReceive('getListenersForEvent')
                ->once()
                ->with($event)
                ->andReturn([$listener1, $listener2]);

            $listener2->shouldNotReceive('__invoke');

            $actual = $this->dispatcher->dispatch($event);

            expect($actual)->toBe($event);
        });

        it('should stop propagation if event is stoppable with event name and return the event object', function () {
            $event = new class() extends StoppableEvent {
                //
            };
            $listener1 = new class {
                public function __invoke(StoppableEvent $event)
                {
                    $event->stopPropagation();
                }
            };
            $listener2 = m::mock(EventDispatcherTestListener::class);
            $this->provider->shouldReceive('getListeners')
                ->once()
                ->with('event.name')
                ->andReturn([$listener1, $listener2]);

            $listener2->shouldNotReceive('__invoke');
            $actual = $this->dispatcher->dispatch($event, 'event.name');
            expect($actual)->toBe($event);
        });

        it('should throw an exception if an exception occurs during listener call', function () {
            $event = new stdClass();
            $listener1 = new class {
                public function __invoke(stdClass $event)
                {
                    throw new Exception('Listener 1 exception');
                }
            };
            $listener2 = new class {
                public function __invoke(stdClass $event)
                {
                    //
                }
            };
            $this->provider->shouldReceive('getListenersForEvent')
                ->once()
                ->with($event)
                ->andReturn([$listener1, $listener2]);

            expect(
                function () use ($event) {
                    $this->dispatcher->dispatch($event);
                }
            )->toThrow(EventListenerHandlingExceptions::class);
        });
    }
)->group('EventDispatcher', 'event');

class EventDispatcherTestListener
{
    public function __invoke(StoppableEvent $event)
    {
        //
    }
}
