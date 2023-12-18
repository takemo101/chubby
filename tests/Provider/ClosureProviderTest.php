<?php

use Takemo101\Chubby\ApplicationContainer;
use Takemo101\Chubby\Bootstrap\Definitions;
use Takemo101\Chubby\Bootstrap\Provider\ClosureProvider;
use Tests\ApplicationTestCase;

describe(
    'ClosureProvider',
    function () {
        test(
            'Set dependencies with ClosureProvider',
            function (object $object) {
                /** @var ApplicationTestCase $this */

                $app = $this->createApplication();

                $app->addProvider(
                    new ClosureProvider(
                        register: fn (Definitions $definitions) => $definitions->add([
                            get_class($object) => $object,
                        ]),
                    ),
                );

                $app->boot();

                $actual = $app->get(get_class($object));

                expect($actual)->toBe($object);
            },
        )->with('objects');

        test(
            'Set dependencies according to the return value of Closure set in ClosureProvider',
            function (object $object) {
                /** @var ApplicationTestCase $this */

                $app = $this->createApplication();

                $original = clone $object;
                $objectName = get_class($object);

                $data = new class(uniqid()) extends stdClass
                {
                    public function __construct(
                        public string $data,
                    ) {
                        //
                    }
                };

                $app->addProvider(
                    new ClosureProvider(
                        register: fn () => [
                            $objectName => $object,
                            stdClass::class => $data,
                        ],
                        boot: fn (ApplicationContainer $container, stdClass $data) => $container->get(
                            $objectName,
                        )->change($data->data),
                    ),
                );

                $app->boot();

                $actual = $app->get(get_class($object));

                expect($actual)->toBe($object);
                expect($actual->data)->not->toBe($original->data);
            },
        )->with('objects');
    }
)->group('ClosureProvider', 'provider');

dataset(
    'objects',
    [
        new class('test01')
        {
            public function __construct(
                public string $data,
            ) {
                //
            }

            public function change(string $data)
            {
                $this->data = $data;
            }
        },
        new class('test02')
        {
            public function __construct(
                public string $data,
            ) {
                //
            }

            public function change(string $data)
            {
                $this->data .= $data;
            }
        },
    ],
);
