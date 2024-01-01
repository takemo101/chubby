<?php

use Takemo101\Chubby\Event\EventMapExtractor;
use Takemo101\Chubby\Event\Attribute\AsEventListener;
use Takemo101\Chubby\Event\PrioritizedListener;

describe(
    'EventMapExtractor',
    function () {

        it('should throw an exception if the listener class does not exist', function () {

            $extractor = new EventMapExtractor();

            $listener = 'NonExistentListener';

            expect(fn () => $extractor->extract($listener))
                ->toThrow(InvalidArgumentException::class);
        });

        it('should extract the default event map if no attributes are present', function () {

            $extractor = new EventMapExtractor();

            $listener = new EventMapExtractorTestListener();

            $expectedMap = [
                stdClass::class => [
                    new PrioritizedListener(
                        classOrObject: $listener,
                    )
                ],
            ];

            $map = $extractor->extract($listener);

            expect($map)->toEqual($expectedMap);
        });

        it('should extract the event map from attributes', function () {

            $extractor = new EventMapExtractor();

            $listener = EventMapExtractorTestListenerWithAttributes::class;

            $expectedMap = [
                'Event1' => [
                    new PrioritizedListener(
                        classOrObject: $listener,
                        method: 'handleEvent1',
                        priority: 10
                    )
                ],
                'Event2' => [
                    new PrioritizedListener(
                        classOrObject: $listener,
                        method: 'handleEvent2',
                        priority: 5
                    )
                ],
            ];

            $map = $extractor->extract($listener);

            expect($map)->toEqual($expectedMap);
        });
    }
)->group('EventMapExtractor', 'event');

class EventMapExtractorTestListener
{
    public function __invoke(stdClass $event)
    {
        //
    }
}


#[AsEventListener(event: 'Event1', method: 'handleEvent1', priority: 10)]
#[AsEventListener(event: 'Event2', method: 'handleEvent2', priority: 5)]
class EventMapExtractorTestListenerWithAttributes
{
    public function handleEvent1()
    {
        //
    }

    public function handleEvent2()
    {
        //
    }
}
