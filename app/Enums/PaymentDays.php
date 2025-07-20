<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class PaymentDays
 *
 * @package App\Enums
 */
final class PaymentDays extends BaseEnum
{
    const ONE_DAY = 1;
    const TWO_DAYS = 2;
    const THREE_DAYS = 3;
    const FOUR_PLUS_DAYS = 4;
    const TEMP_FIXED_DAYS = 5;


    /**
     * Retrieve the price
     *
     * @return string
     */
    public function value()
    {
        return [
            self::ONE_DAY           => 67800,
            self::TWO_DAYS          => 90400 ,
            self::THREE_DAYS        => 113000,
            self::FOUR_PLUS_DAYS    => 141250,
            self::TEMP_FIXED_DAYS   => 6441,
        ][$this->value];
    }

       /**
     * Retrieve the price
     *
     * @return string
     */
    public function valueWithTaxes()
    {
        return [
            self::ONE_DAY           => "$600 + 78.00 (HST)",
            self::TWO_DAYS          => "$800 + 104.00 (HST)",
            self::THREE_DAYS        => "$1,000 + 130.00 (HST)",
            self::FOUR_PLUS_DAYS    => "$1250.00 + 162.50 (HST)",
            self::TEMP_FIXED_DAYS   => "$57 + 7.41 (HST 13%)",
        ][$this->value];
    }

}
