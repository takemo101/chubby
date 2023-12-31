<?php

use Takemo101\Chubby\Event\ClosureListener;
use Takemo101\Chubby\Event\EventListener;
use Takemo101\Chubby\Event\EventRegister;
use Takemo101\Chubby\Event\PrioritizedListener;
use Takemo101\Chubby\Event\StoppableEvent;

describe(
    'EventRegister',
    function () {
        it(
            'should register a listener for the event',
            function (
                $event,
                $listener,
                $priority,
            ) {

                $listener = is_callable($listener)
                    ? ClosureListener::from($listener)
                    : $listener;

                $register = new EventRegister();

                $register->on($event, $listener, $priority);

                $listeners = $register->get($event);

                expect($listeners)->toBeArray();
                expect(count($listeners))->toBe(1);

                $prioritizedListener = $listeners[0];
                expect($prioritizedListener)->toBeInstanceOf(PrioritizedListener::class);
                expect($prioritizedListener->listener)->toBe($listener);
                expect($prioritizedListener->priority)->toBe($priority);
            }
        )->with([
            [
                stdClass::class,
                new class implements EventListener
                {
                    public function __invoke(object $event): void
                    {
                        //
                    }
                },
                10
            ],
            [
                stdClass::class,
                TestEventListener::class,
                20
            ],
            [
                stdClass::class,
                fn () => fn (object $event) => null,
                30
            ],
        ]);

        it('should check if the event has a listener', function () {

            $register = new EventRegister();

            $event =  stdClass::class;
            $listener = TestEventListener::class;
            $priority = 10;

            $register->on($event, $listener, $priority);

            expect($register->has($event))->toBeTrue();
            expect($register->has('nonexistent_event'))->toBeFalse();
        });

        it('should remove a listener for the event', function () {

            $register = new EventRegister();

            $event =  stdClass::class;
            $listener = TestEventListener::class;
            $priority = 10;

            $register->on($event, $listener, $priority);

            expect($register->has($event))->toBeTrue();

            $register->remove($event);

            expect($register->has($event))->toBeFalse();
        });

        it('should convert to an array', function () {

            $register = new EventRegister();

            $event1 =  stdClass::class;
            $listener1 = TestEventListener::class;
            $priority1 = 10;

            $event2 =  TestEvent::class;
            $listener2 = fn (object $event) => null;
            $priority2 = 10;

            $register->on($event1, $listener1, $priority1);
            $register->on($event2, $listener2, $priority2);

            $array = $register->toArray();

            expect($array)->toBeArray();
            expect(count($array))->toBe(2);
            expect($array[$event1])->toBeArray();
            expect($array[$event2])->toBeArray();
            expect(count($array[$event1]))->toBe(1);
            expect(count($array[$event2]))->toBe(1);

            $prioritizedListener1 = $array[$event1][0];
            expect($prioritizedListener1)->toBeInstanceOf(PrioritizedListener::class);
            expect($prioritizedListener1->listener)->toBe($listener1);
            expect($prioritizedListener1->priority)->toBe($priority1);

            $prioritizedListener2 = $array[$event2][0];
            expect($prioritizedListener2)->toBeInstanceOf(PrioritizedListener::class);
            expect($prioritizedListener2->listener)->toBeInstanceOf(ClosureListener::class);
            expect($prioritizedListener2->priority)->toBe($priority2);
        });

        it('should create an instance from an array', function () {
            $event1 =  stdClass::class;
            $listener1 = TestEventListener::class;

            $event2 =  TestEvent::class;
            $listener2 = fn (object $event) => null;

            $array = [
                $event1 => $listener1,
                $event2 => $listener2,
            ];

            $register = EventRegister::fromArray($array);

            expect($register)->toBeInstanceOf(EventRegister::class);

            $listeners1 = $register->get($event1);
            expect($listeners1)->toBeArray();
            expect(count($listeners1))->toBe(1);

            $prioritizedListener1 = $listeners1[0];
            expect($prioritizedListener1)->toBeInstanceOf(PrioritizedListener::class);
            expect($prioritizedListener1->listener)->toBe($listener1);

            $listeners2 = $register->get($event2);
            expect($listeners2)->toBeArray();
            expect(count($listeners2))->toBe(1);

            $prioritizedListener2 = $listeners2[0];
            expect($prioritizedListener2)->toBeInstanceOf(PrioritizedListener::class);
            expect($prioritizedListener2->listener)->toBeInstanceOf(ClosureListener::class);
        });
    }
)->group('EventRegister', 'event');

class TestEventListener implements EventListener
{
    public function __invoke(object $event): void
    {
        //
    }
}

class TestEvent extends StoppableEvent
{
    //
}
