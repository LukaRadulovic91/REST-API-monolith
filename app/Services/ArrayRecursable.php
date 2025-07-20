<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Trait ArrayRecursable
 *
 * @package App\Services
 */
trait ArrayRecursable
{
    /**
     * Filters the given array just as array_filter, but recursively
     *
     * @param array         $input
     * @param callable|null $callback
     *
     * @return array
     */
    public static function arrayFilterRecursive(array $input, callable $callback = null): array
    {
        foreach ($input as &$value) {
            if (is_array($value)) {
                $value = ArrayRecursable::arrayFilterRecursive($value, $callback);
            }
        }

        if (!empty($callback)) {
            return array_filter($input, $callback);
        }

        return array_filter($input);

    }
}
