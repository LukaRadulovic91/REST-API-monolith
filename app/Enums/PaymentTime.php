<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class PaymentTime
 *
 * @package App\Enums
 */
final class PaymentTime extends BaseEnum
{
    const SAME_DAY = 1;
    const ONE_DAY = 2;
    const TWO_DAYS = 3;
    const ONE_WEEK = 4;
    const TWO_WEEKS = 5;
    const MONTHLY = 6;

    /**
     * @param $enumValue
     *
     * @return string|null
     */
    public static function getType($enumValue): string|null
    {
        switch ($enumValue) {
            case self::SAME_DAY:
                return 'Same day';
            case self::ONE_DAY:
                return '1 day';
            case self::TWO_DAYS:
                return '2 days';
            case self::ONE_WEEK:
                return '1 week';
            case self::TWO_WEEKS:
                return '2 weeks';
            case self::MONTHLY:
                return 'Monthly';
            default:
                return null;
        }
    }

}
