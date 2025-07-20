<?php

namespace App\Policies;

use App\Enums\Roles;
use App\Models\Candidate;
use App\Models\User;

/**
 * Class CandidatePolicy
 *
 * @package App\Policies
 */
class CandidatePolicy
{
    /**
     * Determine whether the user can view any models.
     * @param User $user
     *
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param Candidate $candidate
     *
     * @return bool
     */
    public function view(User $user, Candidate $candidate): bool
    {
        return $user->role_id === Roles::CANDIDATE ? ($user->candidate->id === $candidate->id) : true;
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param Candidate $candidate
     *
     * @return bool
     */
    public function edit(User $user, Candidate $candidate): bool
    {
        return $user->role_id === Roles::CANDIDATE ? ($user->candidate->id === $candidate->id) : true;
    }


    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Candidate $candidate
     *
     * @return bool
     */
    public function update(User $user, Candidate $candidate): bool
    {
        return $user->candidate->id === $candidate->id;
    }

    /**
     * @param User $user
     * @param Candidate $candidate
     *
     * @return bool
     */
    public function applyForJob(User $user, Candidate $candidate): bool
    {
        return $user->candidate->id === $candidate->id;
    }

    /**
     * @param User $user
     * @param Candidate $candidate
     *
     * @return bool
     */
    public function myJobs(User $user, Candidate $candidate)
    {
        return $user->candidate->id === $candidate->id;
    }

    /**
     * @param User $user
     * @param Candidate $candidate
     *
     * @return bool
     */
    public function checkIfIsApproved(User $user, Candidate $candidate): bool
    {
        return $user->role_id === Roles::CANDIDATE ? ($user->candidate->id === $candidate->id) : true;
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Candidate $candidate
     *
     * @return bool
     */
    public function delete(User $user, Candidate $candidate): bool
    {
        //
    }

}
