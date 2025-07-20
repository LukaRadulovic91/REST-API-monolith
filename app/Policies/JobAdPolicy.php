<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\JobAd;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Queue\Jobs\Job;

/**
 * Class JobAdPolicy
 *
 * @package App\Policies
 */
class JobAdPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function view(User $user, JobAd $jobAd): bool
    {
        return $user->role_id === Roles::CLIENT ? ($user->client->id === $jobAd->client_id) : true;
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     *
     * @return bool
     */
    public function create(User $user): bool
    {
        return $user->role_id === Roles::CLIENT;
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function update(User $user, JobAd $jobAd): bool
    {
        return $user->role_id === Roles::CLIENT ? $user->client->id === $jobAd->client->id : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function delete(User $user, JobAd $jobAd): bool
    {
        return $user->role_id === Roles::CLIENT ? $user->client->id === $jobAd->client->id : false;
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     *
     * @return bool
     */
    public function getHistory(User $user): bool
    {
        return $user->role_id === Roles::CLIENT;
    }

    /**
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function cancelJobAdByClient(User $user, JobAd $jobAd): bool
    {
        return $user->role_id === Roles::CLIENT ? $user->client->id === $jobAd->client->id : false;
    }

    /**
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function cancelJobAdByCandidate(User $user, JobAd $jobAd): bool
    {
        return $user->role_id === Roles::CANDIDATE ? in_array($user->candidate->id,$jobAd->candidate->pluck('id')->toArray()) : false;
    }

    /**
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function clientFeedback(User $user, JobAd $jobAd): bool
    {
        return $user->role_id === Roles::CLIENT ?  $user->client->id === $jobAd->client_id : false;
    }

    /**
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function candidateFeedback(User $user, JobAd $jobAd): bool
    {
        return $user->role_id === Roles::CANDIDATE ? in_array($user->candidate->id,$jobAd->candidate->pluck('id')->toArray()) : false;
    }

    /**
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function getCandidatesApplied(User $user, JobAd $jobAd): bool
    {
        return $user->client->id === $jobAd->client_id;
    }

    /**
     * @param User $user
     *
     * @return true
     */
    public function getDates(User $user): bool
    {
        return true;
    }

    /**
     * @param User $user
     * @param JobAd $jobAd
     *
     * @return bool
     */
    public function approveCandidate(User $user, JobAd $jobAd): bool
    {
        return $user->client->id === $jobAd->client_id;
    }
}
