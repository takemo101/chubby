<?php

use Takemo101\Chubby\Example;

it('foo', function () {
    $example = new Example();

    $result = $example->foo();

    expect($result)->toBe('bar');
});
