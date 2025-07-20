<?php

namespace App\Enums;

use App\BaseEnum;

/**
 * Class ProfileStatuses
 *
 * @package App\Enums
 */
final class ProfileStatuses extends BaseEnum
{
    const PENDING_REVIEW = 1;
    const APPROVED = 2;
    const REJECTED = 3;
}
