<?php

namespace Takemo101\Chubby\Clock;

use DateTimeZone;
use DateTimeImmutable;

/**
 * Provides the current time in the application's standard format.
 */
class ApplicationClock implements Clock
{
    public const Format = 'Y-m-d H:i:s';

    /**
     * constructor
     *
     * @param DateTimeZone $timezone
     */
    public function __construct(
        private readonly DateTimeZone $timezone,
    ) {
        //
    }

    /**
     * {@inheritDoc}
     */
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable('now', $this->timezone);
    }

    /**
     * {@inheritDoc}
     */
    public function __toString(): string
    {
        return $this->now()->format(static::Format);
    }

    /**
     * Create a new instance from the specified timezone.
     *
     * @param string $timezone
     * @return self
     */
    public static function fromTimezoneString(string $timezone = 'UTC'): self
    {
        return new self(new DateTimeZone($timezone));
    }
}
