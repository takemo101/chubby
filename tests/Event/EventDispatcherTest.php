<?php

use Psr\EventDispatcher\ListenerProviderInterface;
use Takemo101\Chubby\Event\EventDispatcher;
use Takemo101\Chubby\Event\StoppableEvent;
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

            $listener1 = new class
            {
                public function __invoke(stdClass $event)
                {
                    //
                }
            };
            $listener2 = new class
            {
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

        it('should stop propagation if event is stoppable', function () {
            $event = new class() extends StoppableEvent
            {
                //
            };

            $listener1 = new class
            {
                public function __invoke(StoppableEvent $event)
                {
                    $event->stopPropagation();
                }
            };
            $listener2 =  m::mock(EventDispatcherTestListener::class);

            $this->provider->shouldReceive('getListenersForEvent')
                ->once()
                ->with($event)
                ->andReturn([$listener1, $listener2]);

            $listener2->shouldNotReceive('__invoke');

            $actual = $this->dispatcher->dispatch($event);

            expect($actual)->toBe($event);
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
