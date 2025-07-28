<?php

namespace App\Repositories\API;

use App\Enums\Roles;
use App\Models\Candidate;
use App\Models\JobAd;
use App\Models\User;

/**
 * Class JobAdRepository
 *
 * @package App\Repositories\API
 */
final class CandidateRepository
{
    /**
     * JobAd constructor.
     *
     * @param Candidate $model
     */
    public function __construct(
        private readonly Candidate $model
    ) {}

    /**
     * @param JobAd $jobAd
     *
     * @return User
     */
    public function getUser(JobAd $jobAd): User
    {
        return User::rightJoin('expo_tokens as et', 'et.owner_id', '=','users.id')
            ->join('clients as c', 'c.user_id', '=', 'users.id')
            ->join('job_ads as ja', 'ja.client_id', '=', 'c.id')
            ->where(function($query) use ($jobAd) {
                $query->where('role_id', '=', Roles::CLIENT)
                    ->where('ja.id', $jobAd->id);
            })
            ->whereNull('users.deleted_at')
            ->select('users.id')
            ->first();
    }
}
