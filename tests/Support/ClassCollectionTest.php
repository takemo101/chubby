<?php

use Takemo101\Chubby\Support\ClassCollection;

describe(
    'ClassCollection',
    function () {
        it('can add classes to the collection', function () {
            $collection = new class() extends ClassCollection
            {
                //
            };

            $class = new class() extends stdClass
            {
                //
            };

            $collection->add(stdClass::class);
            $collection->add($class);

            expect($collection->classes())->toEqual([
                stdClass::class,
                $class,
            ]);
        });

        it('throws an exception when adding a non-existent class', function () {
            $collection = new class() extends ClassCollection
            {
                //
            };

            expect(fn () => $collection->add('NonExistentClass'))
                ->toThrow(RuntimeException::class, 'Class "NonExistentClass" does not exist.');
        });

        it('throws an exception when adding a class that is not a subclass', function () {
            $collection = new class() extends ClassCollection
            {
                //
            };

            expect(fn () => $collection->add(DateTimeZone::class))
                ->toThrow(RuntimeException::class, 'Class "DateTimeZone" is not a subclass of "stdClass".');
        });

        it('can clear the collection', function () {
            $collection = new class() extends ClassCollection
            {
                //
            };

            $collection->add(stdClass::class);
            $collection->add(stdClass::class);

            $collection->clear();

            expect($collection->classes())->toEqual([]);
        });

        it('can create an empty collection', function () {
            $collection = new class() extends ClassCollection
            {
                //
            };

            expect($collection->classes())->toEqual([]);
        });
    }
)->group('ClassCollection', 'support');
