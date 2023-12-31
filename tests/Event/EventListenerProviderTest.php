<?php

use Takemo101\Chubby\Event\EventListenerProvider;
use Takemo101\Chubby\Event\EventRegister;
use Takemo101\Chubby\Event\EventListenerResolver;
use Takemo101\Chubby\Event\PrioritizedListener;
use Mockery as m;
use Takemo101\Chubby\Event\AliasableEvent;

describe(
    'EventListenerProvider',
    function () {

        it(
            'returns listeners for event',
            function () {

                $prioritized1 = PrioritizedListener::from(TestEventListener::class, 1);
                $prioritized2 = PrioritizedListener::from(TestEventListener::class, 2);

                $register = m::mock(EventRegister::class);
                $register->shouldReceive('get')
                    ->andReturn([
                        $prioritized1,
                        $prioritized2,
                    ]);

                $resolver = m::mock(EventListenerResolver::class);
                $resolver->shouldReceive('resolve')
                    ->with(TestEventListener::class)
                    ->andReturn(new TestEventListener());

                // Create an instance of EventListenerProvider with the mocks
                $listenerProvider = new EventListenerProvider($register, $resolver);

                // Create a mock for the event object
                $event = Mockery::mock();

                $listeners = $listenerProvider->getListenersForEvent($event);

                // Assert that the returned value is an iterable
                expect($listeners)->toBeIterable();

                // Assert that the listeners are returned in the correct order
                expect($listeners)->toHaveCount(2);
                expect($listeners)->each(
                    fn ($listener) => $listener->toBeInstanceOf(TestEventListener::class),
                );
            }
        );

        it('should return listeners for event with alias', function () {

            $prioritized1 = PrioritizedListener::from(TestEventListener::class, 1);
            $prioritized2 = PrioritizedListener::from(TestEventListener::class, 2);

            $register = m::mock(EventRegister::class);
            $register->shouldReceive('get')
                ->andReturn([
                    $prioritized1,
                    $prioritized2,
                ]);

            $resolver = m::mock(EventListenerResolver::class);
            $resolver->shouldReceive('resolve')
                ->with(TestEventListener::class)
                ->andReturn(new TestEventListener());

            // Create an instance of EventListenerProvider with the mocks
            $listenerProvider = new EventListenerProvider($register, $resolver);

            // Create a mock for the event object
            $event = Mockery::mock(AliasableEvent::class);
            $event->shouldReceive('getAlias')
                ->andReturn(TestEvent::class);

            $listeners = $listenerProvider->getListenersForEvent($event);

            // Assert that the returned value is an iterable
            expect($listeners)->toBeIterable();

            // Assert that the listeners are returned in the correct order
            expect($listeners)->toHaveCount(2);
            expect($listeners)->each(
                fn ($listener) => $listener->toBeInstanceOf(TestEventListener::class)
            );
        });
    }
)->group('EventListenerProvider', 'event');
