<?php

use Takemo101\Chubby\Clock\ApplicationClock;

describe(
    'ApplicationClock',
    function () {
        it('returns the current time in the specified timezone', function () {
            $timezone = new DateTimeZone('Asia/Tokyo');
            $clock = new ApplicationClock($timezone);

            $currentTime = $clock->now();

            expect($currentTime)->toBeInstanceOf(DateTimeImmutable::class);
            expect($currentTime->getTimezone())->toEqual($timezone);
        });

        it('returns the current time in the default timezone if not specified', function () {
            $clock = ApplicationClock::fromTimezoneString();

            $currentTime = $clock->now();

            expect($currentTime)->toBeInstanceOf(DateTimeImmutable::class);
            expect($currentTime->getTimezone())->toEqual(new DateTimeZone('UTC'));
        });

        it('returns the current time in the application\'s standard format as a string', function () {
            $clock = ApplicationClock::fromTimezoneString('America/New_York');

            $currentTimeString = (string) $clock;

            expect($currentTimeString)->toMatch('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/');
        });
    },
)->group('ApplicationClock', 'clock');
