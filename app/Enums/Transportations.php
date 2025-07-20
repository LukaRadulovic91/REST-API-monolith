<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class Transportations
 *
 * @package App\Enums
 */
final class Transportations extends BaseEnum
{
    const BY_CAR = 1;
    const PUBLIC = 2;

    /**
     * @param $enumValue
     *
     * @return string|null
     */
    public static function getTransportation($enumValue)
    {
        switch ($enumValue) {
            case self::BY_CAR:
                return 'By car';
            case self::PUBLIC:
                return 'Public';
            default:
                return null;
        }
    }
}
