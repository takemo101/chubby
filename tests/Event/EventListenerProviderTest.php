<?php

use Takemo101\Chubby\Event\EventListenerProvider;
use Takemo101\Chubby\Event\EventRegister;
use Takemo101\Chubby\Event\EventListenerResolver;
use Takemo101\Chubby\Event\Attribute\AsEvent;
use Takemo101\Chubby\Event\PrioritizedListener;
use Mockery as m;
use Takemo101\Chubby\Event\Exception\EventListenerResolveException;

describe(
    'EventListenerProvider',
    function () {

        it('returns the correct listeners for the event', function () {
            $register = m::mock(EventRegister::class);
            $resolver = m::mock(EventListenerResolver::class);

            // Create an instance of the EventListenerProvider
            $provider = new EventListenerProvider($register, $resolver);

            $event = new EventListenerProviderTestEvent();

            $listener = new EventListenerProviderTestListener();

            // Mock the EventRegister to return an empty array of priorities
            $register->shouldReceive('get')
                ->with(stdClass::class)
                ->andReturn([
                    new PrioritizedListener(
                        classOrObject: EventListenerProviderTestListener::class,
                        priority: 0,
                    ),
                ]);

            $resolver->shouldReceive('resolve')
                ->with(EventListenerProviderTestListener::class)
                ->andReturn($listener);

            // Call the getListenersForEvent method and assert the result
            $actual = $provider->getListenersForEvent($event);

            expect($actual)->toBeIterable();
            expect($actual)->toHaveCount(1);
            expect(iterator_to_array($actual)[0])->toBeCallable();
        });

        it('throws an exception if the listener is not callable or an object', function () {
            // Create a mock EventRegister and EventListenerResolver
            $register = m::mock(EventRegister::class);
            $resolver = m::mock(EventListenerResolver::class);

            // Create an instance of the EventListenerProvider
            $provider = new EventListenerProvider($register, $resolver);

            $event = new EventListenerProviderTestEvent();

            // Mock the EventRegister to return an array of priorities
            $register->shouldReceive('get')
                ->with(stdClass::class)
                ->andReturn([
                    new PrioritizedListener(
                        classOrObject: EventListenerProviderTestListener::class,
                        method: 'notCallable',
                        priority: 0,
                    ),
                ]);


            $resolver->shouldReceive('resolve')
                ->with(EventListenerProviderTestListener::class)
                ->andReturn(new EventListenerProviderTestListener());

            expect(fn () => $provider->getListenersForEvent($event))
                ->toThrow(EventListenerResolveException::class);
        });
    },
)->group('EventListenerProvider', 'event');

class EventListenerProviderTestListener
{
    public function __invoke(stdClass $event)
    {
        //
    }
}

#[AsEvent(stdClass::class)]
class EventListenerProviderTestEvent
{
    //
}
