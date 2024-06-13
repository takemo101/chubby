<?php

namespace Takemo101\Chubby\Http\Routing;

use InvalidArgumentException;

/**
 * Route pattern for the domain.
 */
readonly class DomainPattern
{
    /**
     * constructor
     *
     * @param string $pattern
     */
    public function __construct(
        public string $pattern
    ) {
        // The pattern must not contain a slash.
        assert(
            !empty($pattern),
            'The pattern is empty.'
        );

        // The pattern must not contain a slash.
        assert(
            strpos($pattern, '/') === false,
            'The pattern must not contain a slash.'
        );
    }

    /**
     * Get the pattern for the domain route.
     *
     * @return string
     * @throws InvalidArgumentException
     */
    public function replaceDotsToSlashes(): string
    {

        // Split the pattern by the curly braces.
        $splits = preg_split('/(\{[^}]*\})/', $this->pattern, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);

        if ($splits === false) {
            throw new InvalidArgumentException('Failed to split the pattern.');
        }

        // Replace the dots with slashes.
        $replaced = implode(
            '',
            array_map(
                fn ($part) => strpos($part, '{') === 0 ? $part : str_replace('.', '/', $part),
                $splits,
            )
        );

        return $replaced;
    }
}
