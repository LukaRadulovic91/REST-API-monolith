<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class RecallTime
 *
 * @package App\Enums
 */
final class RecallTime extends BaseEnum
{
    const SIXTY_MINUTES = 1;
    const FOURTY_FIVE_MINUTES = 2;
    const THIRTY_MINUTES = 3;

    public static function getMinutes($enumValue)
    {
        switch ($enumValue) {
            case self::SIXTY_MINUTES:
                return 60;
            case self::FOURTY_FIVE_MINUTES:
                return 45;
            case self::THIRTY_MINUTES:
                return 30;
            default:
                return null;
        }
    }
}
