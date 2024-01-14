<?php

use Takemo101\Chubby\Event\EventTypeInferencer;
use Takemo101\Chubby\Event\Exception\EventTypeInferenceException;

describe(
    'EventTypeInferencer',
    function () {

        it('should infer event class name from named type', function () {

            $inferencer = new EventTypeInferencer();

            $class = new ReflectionClass(EventTypeInferencerTestListener::class);
            $methodName = '__invoke';

            $result = $inferencer->inference($class, $methodName);

            expect($result)->toBeArray();
            expect($result)->toHaveLength(1);
            expect($result[0])->toBe(stdClass::class);
        });

        it('should infer event class names from union type', function () {

            $inferencer = new EventTypeInferencer();

            $class = new ReflectionClass(EventTypeInferencerTestListener::class);
            $methodName = 'unionTypeMethod';

            $result = $inferencer->inference($class, $methodName);

            expect($result)->toBeArray();
            expect($result)->toHaveLength(2);
            expect($result)->toContain(EventTypeInferencerTestData::class);
            expect($result)->toContain(stdClass::class);
        });

        it('should throw an exception if method does not exist', function () {

            $inferencer = new EventTypeInferencer();

            $class = new ReflectionClass(EventTypeInferencerTestListener::class);
            $methodName = 'nonExistentMethod';

            expect(fn () => $inferencer->inference($class, $methodName))
                ->toThrow(EventTypeInferenceException::class);
        });

        it('should throw an exception if method has no parameters', function () {

            $inferencer = new EventTypeInferencer();

            $class = new ReflectionClass(EventTypeInferencerTestListener::class);
            $methodName = 'methodWithNoParameters';

            expect(fn () => $inferencer->inference($class, $methodName))
                ->toThrow(EventTypeInferenceException::class);
        });

        it('should throw an exception if method has no type', function () {

            $inferencer = new EventTypeInferencer();

            $class = new ReflectionClass(EventTypeInferencerTestListener::class);
            $methodName = 'methodWithNoType';

            expect(fn () => $inferencer->inference($class, $methodName))
                ->toThrow(EventTypeInferenceException::class);
        });
    }
)->group('EventTypeInferencer', 'event');

class EventTypeInferencerTestData
{
}

class EventTypeInferencerTestListener
{
    public function __invoke(stdClass $event)
    {
    }

    public function unionTypeMethod(stdClass|EventTypeInferencerTestData $event)
    {
    }

    public function methodWithNoParameters()
    {
    }

    public function methodWithNoType($event)
    {
    }
}
