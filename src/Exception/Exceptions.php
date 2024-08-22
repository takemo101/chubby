<?php

namespace Takemo101\Chubby\Exception;

use Takemo101\Chubby\Contract\Throwables;
use Exception;
use InvalidArgumentException;
use Throwable;

/**
 * Multiple exceptions occurred.
 *
 * @immutable
 */
class Exceptions extends Exception implements Throwables
{
    public const Code = 0;

    /**
     * @var Throwable[]
     */
    private readonly array $throwables;

    /**
     * constructor
     *
     * @param Throwable ...$throwables The exceptions that occurred.
     */
    final public function __construct(
        Throwable ...$throwables
    ) {
        $errorCount = count($throwables);

        assert(
            $errorCount > 0,
            'At least one exception must be specified.',
        );

        $this->throwables = $throwables;

        parent::__construct(
            message: "Multiple exceptions occurred: {$errorCount} errors.",
            code: static::Code,
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
