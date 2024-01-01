<?php

use Takemo101\Chubby\Event\EventRegister;
use Takemo101\Chubby\Event\EventMapExtractor;
use Takemo101\Chubby\Event\PrioritizedListener;
use Mockery as m;

describe(
    'EventRegister',
    function () {
        beforeEach(function () {
            $this->extractor = m::mock(EventMapExtractor::class);
            $this->eventRegister = new EventRegister($this->extractor);
        });

        it('should register a listener for the event', function () {
            $listener = EventRegisterTestListener::class;

            $map = [
                'event1' => [
                    new PrioritizedListener(
                        classOrObject: $listener,
                        priority: 0,
                    ),
                ],
                'event2' => [
                    new PrioritizedListener(
                        classOrObject: $listener,
                        priority: 1,
                    ),
                ],
            ];

            $this->extractor->shouldReceive('extract')
                ->with($listener)
                ->andReturn($map);

            $this->eventRegister->on($listener);

            expect($this->eventRegister->get('event1'))->toBe($map['event1']);
            expect($this->eventRegister->get('event2'))->toBe($map['event2']);
        });

        it('should throw an exception if the listener is invalid', function () {
            $listener = 'Invalid\Listener';

            $eventRegister = new EventRegister();

            expect(fn () => $eventRegister->on($listener))
                ->toThrow(InvalidArgumentException::class);
        });

        it('should register a listener for the event with priority', function () {
            $event = 'event1';
            $listener1 = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 0,
            );
            $listener2 = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 1,
            );

            $this->eventRegister->listen($event, $listener1, $listener2);

            expect($this->eventRegister->get($event))->toEqual([$listener1, $listener2]);
        });

        it('should return the listeners for the event', function () {
            $event = 'event1';
            $listener1 = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 0,
            );
            $listener2 = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 1,
            );

            $this->eventRegister->listen($event, $listener1, $listener2);

            expect($this->eventRegister->get($event))->toEqual([$listener1, $listener2]);
        });

        it('should return an empty array if the event has no listeners', function () {
            $event = 'event1';

            expect($this->eventRegister->get($event))->toEqual([]);
        });

        it('should return true if the event has listeners', function () {
            $event = 'event1';
            $listener = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 0,
            );

            $this->eventRegister->listen($event, $listener);

            expect($this->eventRegister->has($event))->toBeTrue();
        });

        it('should return false if the event has no listeners', function () {
            $event = 'event1';

            expect($this->eventRegister->has($event))->toBeFalse();
        });

        it('should remove the listeners for the event', function () {
            $event = 'event1';
            $listener = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 0,
            );

            $this->eventRegister->listen($event, $listener);

            expect($this->eventRegister->has($event))->toBeTrue();

            $this->eventRegister->remove($event);

            expect($this->eventRegister->has($event))->toBeFalse();
        });

        it('should return the listeners as an array', function () {
            $event1 = 'event1';
            $listener1 = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 0,
            );
            $listener2 = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 1,
            );

            $event2 = 'event2';
            $listener3 = new PrioritizedListener(
                classOrObject: EventRegisterTestListener::class,
                priority: 0,
            );

            $this->eventRegister->listen($event1, $listener1, $listener2);
            $this->eventRegister->listen($event2, $listener3);

            $expected = [
                $event1 => [$listener1, $listener2],
                $event2 => [$listener3],
            ];

            expect($this->eventRegister->toArray())->toEqual($expected);
        });

        it('should create an instance from an array', function () {
            $event1 = 'event1';
            $event2 = 'event2';

            $listen = [EventRegisterTestListener::class];

            $instance = EventRegister::fromArray($listen);

            expect($instance)->toBeInstanceOf(EventRegister::class);
            expect($instance->get($event1))->toEqual([]);
            expect($instance->get($event2))->toEqual([]);
        });
    }
)->group('EventRegister', 'event');


class EventRegisterTestListener
{
    public function __invoke(stdClass $event)
    {
        //
    }
}
