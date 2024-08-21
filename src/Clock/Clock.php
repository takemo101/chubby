<?php

namespace Takemo101\Chubby\Clock;

use DateTimeZone;
use Psr\Clock\ClockInterface;
use Stringable;

interface Clock extends ClockInterface, Stringable
{
    /**
     * Create a new instance with the specified timezone.
     *
     * @param DateTimeZone $timezone
     * @return static
     */
    public function withTimezone(DateTimeZone $timezone): static;

    /**
     * Get the timezone.
     *
     * @return DateTimeZone
     */
    public function getTimezone(): DateTimeZone;
}
