<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class MediaType
 *
 * @package App\Enums
 */
final class MediaType extends BaseEnum
{
    const CV = 1;
    const CERTIFICATE = 2;

    /**
     * @param $value
     *
     * @return string
     */
    public static function getKey($value): string
    {
        switch ($value) {
            case self::CV:
                return 'CV';
            case self::CERTIFICATE:
                return 'CERTIFICATE';
            default:
                throw new \InvalidArgumentException("Invalid MediaType value: $value");
        }
    }
}
