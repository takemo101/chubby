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
    public const Message = 'Multiple exceptions occurred';

    public const Code = 0;

    /**
     * @var Throwable[]
     */
    private readonly array $throwables;

    /**
     * constructor
     *
     * @param Throwable ...$throwables The exceptions that occurred.
     * @throws InvalidArgumentException If no exceptions were provided.
     */
    final public function __construct(
        Throwable ...$throwables
    ) {
        $errorCount = count($throwables);

        if ($errorCount === 0) {
            throw new InvalidArgumentException('No exceptions were provided.');
        }

        $this->throwables = $throwables;

        $message = static::Message;

        parent::__construct(
            message: "{$message}: {$errorCount} errors.",
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
