<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * Class JobAdPolicy
 *
 * @package App\Policies
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function checkIsUserApproved(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function toggleNotificationsStatus(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function changeImage(User $user): bool
    {
        return  true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function softDeleteUser(User $user): bool
    {
        return  true;
    }
}
