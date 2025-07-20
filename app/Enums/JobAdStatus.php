<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class JobAdStatus
 *
 * @package App\Enums
 */
final class JobAdStatus extends BaseEnum
{
    const PENDING_REVIEW = 1;
    const APPROVED = 2;
    const BOOKED = 3;
    const CANCELLED = 4;
    const REJECTED = 5;
    const COMPLETED = 6;
    const ACTIVE = 7;
    const APPLIED = 8;
}
