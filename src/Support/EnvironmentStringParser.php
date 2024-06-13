<?php

namespace Takemo101\Chubby\Support;

/**
 * Parse environment string to primitive.
 */
class EnvironmentStringParser
{
    /**
     * Parse environment string to primitive.
     *
     * @param string $value
     * @return mixed
     */
    public function parse(string $value): mixed
    {
        $lower = strtolower($value);

        return match ($lower) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => preg_match('/\A([\'"])(.*)\1\z/', $value, $matches)
                ? $matches[2]
                : $value,
        };
    }
}
