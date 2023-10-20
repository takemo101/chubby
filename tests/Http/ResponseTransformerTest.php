<?php

use Tests\AppTestCase;

describe(
    'response transformer',
    function () {
        test(
            'transform',
            function ($data) {
                /** @var AppTestCase $this */

                $request = $this->createRequest();
                $response = $this->createResponse();
            },
        );
    }
)->group('transformer');
