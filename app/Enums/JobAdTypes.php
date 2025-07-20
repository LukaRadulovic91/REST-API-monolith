<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class JobAdTypes
 *
 * @package App\Enums
 */
final class JobAdTypes extends BaseEnum
{
    const PERMANENT_FULL_TIME = 1;
    const TEMPORARY = 2;
    const PERMANENT_PART_TIME = 3;

    public static function getType($enumValue)
    {
        switch ($enumValue) {
            case self::PERMANENT_FULL_TIME:
                return 'Permanent full time';
            case self::PERMANENT_PART_TIME:
                return  'Permanent part time';
            case self::TEMPORARY:
                return 'Temporary';
            default:
                return null;
        }
    }
}
