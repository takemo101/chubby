<?php

namespace Takemo101\Chubby\Exception;

use Takemo101\Chubby\Contract\Throwables;
use Exception;
use Throwable;

/**
 * Multiple exceptions occurred.
 *
 * @immutable
 */
class Exceptions extends Exception implements Throwables
{
    public const Message = 'Multiple exceptions occurred';

    public const Code = 0;

    /**
     * @var Throwable[]
     */
    private readonly array $throwables;

    /**
     * constructor
     *
     * @param Throwable ...$throwables
     */
    final public function __construct(
        Throwable ...$throwables
    ) {
        $this->throwables = $throwables;

        parent::__construct(
            message: static::Message,
            code: static::Code,
        );
    }

    /**
     * Add throwable objects.
     *
     * @param Throwable ...$throwables
     * @return static
     */
    public function addThrowables(Throwable ...$throwables): static
    {
        return new static(
            ...$this->throwables,
            ...$throwables,
        );
    }

    /**
     * {@inheritDoc}
     */
    public function getThrowables(): array
    {
        return $this->throwables;
    }
}
