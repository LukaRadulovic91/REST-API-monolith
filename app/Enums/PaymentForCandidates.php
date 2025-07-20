<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class PaymentForCandidates
 *
 * @package App\Enums
 */
final class PaymentForCandidates extends BaseEnum
{
    const CHEQUE = 1;
    const E_TRANSFER = 2;
    const DIRECT_DEPOSIT = 3;
}
