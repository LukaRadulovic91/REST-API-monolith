<?php

namespace App\Repositories\API;

use DB;
use Carbon\Carbon;
use App\Enums\Roles;
use App\Enums\JobAdStatus;
use App\Models\Candidate;
use App\Models\CandidateJobAd;
use App\Models\JobAd;
use App\Models\User;

/**
 * Class JobAdRepository
 *
 * @package App\Repositories\API
 */
class JobAdRepository
{
    /**
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function getJobAd(JobAd $jobAd): mixed
    {
        $jobs = $jobAd;

        if (auth()->user()->role_id === Roles::CANDIDATE) {
            $jobs->leftJoin('candidates_job_ads as cja', function ($join) use ($jobAd) {
                $join->on('cja.job_ad_id', '=', 'job_ads.id')
                    ->where('cja.candidate_id', '=', auth()->user()->candidate->id);
            });
        }
        $jobs->with(['client', 'shifts', 'client.user', 'position']);

        return $jobs->where('job_ads.id', $jobAd->id)->first();
    }


    /**
     * @param User $user
     *
     * @return mixed
     */
    public function getJobAdsForClients(User $user): mixed
    {
        return DB::table('job_ads as ja')
            ->leftJoin('clients as c', 'c.id', '=', 'ja.client_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->where(function ($query) use ($user) {
                $query->where('u.id', $user->id)
                      ->where('ja.client_id', $user->client->id);
            })
            ->where('ja.job_ad_status_id', '!=', JobAdStatus::PENDING_REVIEW)
            ->whereNull('ja.deleted_at')
            ->whereNull('u.deleted_at')
            ->select([
                'ja.id',
                'ja.client_id',
                'ja.job_ad_type',
                'ja.title',
                'ja.job_description',
                'ja.pay_rate',
                'ja.payment_time',
                'ja.years_experience',
                'ja.permament_start_date',
                'ja.client_feedback',
                'ja.is_active',
                'ja.lunch_break',
                'ja.lunch_break_duration',
                'c.company_name as company_name',
                'c.office_address as office_address',
                'u.first_name',
                'u.last_name'
            ])
            ->get();
    }

    /**
     * @param Candidate $candidate
     *
     * @return mixed
     */
    public function getJobAdsForCandidate(Candidate $candidate): mixed
    {
        $queryResult = CandidateJobAd::leftJoin('job_ads as ja', 'candidates_job_ads.job_ad_id', '=', 'ja.id')
            ->leftJoin('clients as c', 'ja.client_id', '=', 'c.id')
            ->leftJoin('users as u', 'c.user_id', '=', 'u.id')
            ->where('candidates_job_ads.candidate_id', '=', $candidate->id)
            ->whereNull('ja.deleted_at')
            ->whereNull('u.deleted_at')
            ->whereNot('candidates_job_ads.job_ad_status', JobAdStatus::CANCELLED)
            ->select([
                'ja.id as id',
                'ja.client_id',
                'ja.job_ad_type',
                'ja.title',
                'ja.job_description',
                'ja.pay_rate',
                'ja.payment_time',
                'ja.years_experience',
                'ja.permament_start_date',
                'ja.client_feedback',
                'ja.is_active',
                'ja.lunch_break',
                'ja.lunch_break_duration',
                'c.company_name',
                'c.office_address',
                'u.first_name',
                'u.last_name',
                'candidates_job_ads.candidate_id',
                'candidates_job_ads.job_ad_status',
                'c.payment_for_candidates'
            ])
            ->selectRaw("(SELECT GROUP_CONCAT(CONCAT(start_date, ',', end_date, ',', start_time, ',', end_time) SEPARATOR '|') FROM shifts as s WHERE s.job_ad_id = ja.id) as shifts")
            ->get();

        $jobAdsArray = $queryResult->map(function ($jobAdData) {
            $shifts = [];
            if ($jobAdData->shifts) {
                $shifts = collect(explode('|', $jobAdData->shifts))->map(function ($shift) {
                    list($startDate, $endDate, $startTime, $endTime) = explode(',', $shift);
                    return [
                        'start_date' => $startDate,
                        'end_date' => $endDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime
                    ];
                })->toArray();

            }
            return array_merge($jobAdData->toArray(), ['shifts' => $shifts]);
        });

        return  $jobAdsArray;
    }

    /**
     * @return mixed
     */
    public function getJobAdsForCandidates(): mixed
    {
        return DB::table('job_ads as ja')
            ->leftJoin('clients as c', 'c.id', '=', 'ja.client_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->where('ja.job_ad_status_id', '!=', JobAdStatus::PENDING_REVIEW)
            ->whereNull('u.deleted_at')
            ->whereNull('ja.deleted_at')
            ->get();
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function getJobAdsHistory(User $user): mixed
    {
        return JobAd::leftJoin('clients as c', 'c.id', '=', 'job_ads.client_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('positions as p', 'p.id', '=', 'job_ads.title')
            ->where(static function ($query) use ($user) {
                $query->where('u.id', $user->id)
                    ->where('job_ads.client_id', $user->client->id);
            })
            ->whereNull('u.deleted_at')
            ->whereNull('job_ads.deleted_at')
            ->with('shifts')
            ->select([
                'job_ads.id as id',
                'job_ads.client_id' ,
                'job_ads.job_ad_type' ,
                'job_ads.job_ad_status_id',
                'p.title as title' ,
                'job_ads.job_description' ,
                'job_ads.pay_rate' ,
                'job_ads.payment_time',
                'job_ads.years_experience' ,
                'job_ads.permament_start_date' ,
                'job_ads.client_feedback',
                'job_ads.is_active' ,
                'job_ads.lunch_break',
                'job_ads.lunch_break_duration' ,
                'c.company_name',
                'c.office_address',
                'u.first_name',
                'u.last_name'
            ])
            ->get();
    }


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function getJobAdsForUsers(array $data): mixed
    {
        $currentMonthStart = array_key_exists('start_date', $data)
            ? Carbon::parse($data['start_date'])
            : Carbon::now()->startOfDay();

        $currentMonthEnd = array_key_exists('end_date', $data)
            ? Carbon::parse($data['end_date'])
            : Carbon::now()->endOfDay();

        $jobs = JobAd::leftJoin('clients as c', 'c.id', '=', 'job_ads.client_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->leftJoin('positions as p', 'p.id', '=', 'job_ads.title')
            ->whereNull('job_ads.deleted_at')
            ->where(static function ($query) use ($currentMonthStart, $currentMonthEnd) {
                $query->whereBetween('job_ads.permament_start_date', [$currentMonthStart, $currentMonthEnd])
                    ->orWhereHas('shifts', static function ($query) use ($currentMonthStart, $currentMonthEnd) {
                        $query->whereBetween('shifts.start_date', [$currentMonthStart, $currentMonthEnd]);
                    });
            })
            ->where('job_ads.job_ad_status_id', '!=', JobAdStatus::PENDING_REVIEW)
            ->whereNull('u.deleted_at')
            ->with('shifts')
            ->select([
                'job_ads.id as id',
                'job_ads.client_id' ,
                'job_ads.job_ad_type' ,
                'job_ads.job_ad_status_id',
                'p.title as title' ,
                'job_ads.job_description' ,
                'job_ads.pay_rate' ,
                'job_ads.payment_time',
                'job_ads.years_experience' ,
                'job_ads.permament_start_date' ,
                'job_ads.client_feedback',
                'job_ads.is_active' ,
                'job_ads.lunch_break',
                'job_ads.lunch_break_duration' ,
                'c.company_name',
                'c.office_address',
                'u.first_name',
                'u.last_name'
            ]);

        if (auth()->user()->role_id === Roles::CLIENT)
        {
            $user = auth()->user();
            $jobs->where(static function ($query) use ($user) {
                $query->where('u.id', $user->id)
                    ->where('job_ads.client_id', $user->client->id);
            });
        }

        $this->getSearchResults($data, $jobs);

        return $jobs->get();
    }


    /**
     * @param array $data
     *
     * @return mixed
     */
    public function getDates(array $data): mixed
    {

        $currentMonthStart = array_key_exists('start_date', $data)
            ? Carbon::parse($data['start_date'])
            : Carbon::today()->startOfMonth();

        $currentMonthEnd = array_key_exists('end_date', $data)
            ? Carbon::parse($data['end_date'])
            : Carbon::today()->endOfMonth();

        $jobs = JobAd::leftJoin('clients as c', 'c.id', '=', 'job_ads.client_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->where('job_ads.job_ad_status_id', '!=', JobAdStatus::PENDING_REVIEW)
            ->whereNull('job_ads.deleted_at')
            ->whereNull('u.deleted_at')
            ->where(static function ($query) use ($currentMonthStart, $currentMonthEnd) {
                $query->whereBetween('job_ads.permament_start_date', [$currentMonthStart, $currentMonthEnd])
                    ->orWhereHas('shifts', static function ($query) use ($currentMonthStart, $currentMonthEnd) {
                        $query->whereBetween('shifts.start_date', [$currentMonthStart, $currentMonthEnd]);
                    });
            })
            ->with(['shifts' => function($query) use ($currentMonthStart, $currentMonthEnd) {
                $query->whereBetween('start_date', [$currentMonthStart, $currentMonthEnd]);
            }])
            ->select([
                'job_ads.id as id',
                'job_ads.permament_start_date'
            ]);



        if (auth()->user()->role_id === Roles::CLIENT)
        {
            $user = auth()->user();
            $jobs->where(static function ($query) use ($user) {
                $query->where('u.id', $user->id)
                    ->where('job_ads.client_id', $user->client->id);
            });
        }

        $this->getSearchResults($data, $jobs);

        $dates = $jobs->get()->flatMap(function ($job) {
            $jobStartDate = $job->permament_start_date;
            $shiftsStartDates = optional($job->shifts)->pluck('start_date')->toArray();
            return array_merge([$jobStartDate], $shiftsStartDates);
        });


        return $dates;
    }

    /**
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function isCancelledByCandidate(JobAd $jobAd): mixed
    {
        return CandidateJobAd::leftJoin('job_ads as ja', 'ja.id', '=', 'candidates_job_ads.job_ad_id')
            ->leftJoin('candidates as c', 'c.id', 'candidates_job_ads.candidate_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->where('candidates_job_ads.job_ad_status', '=', JobAdStatus::CANCELLED)
            ->where('candidates_job_ads.job_ad_id', '=', $jobAd->id)
            ->whereNull('u.deleted_at')
            ->select(
                'c.id',
                'u.first_name',
                'u.last_name',
                'candidates_job_ads.reason_of_cancellation'
            )->first();
    }

    /**
     * @param $data
     * @param $jobs
     *
     * @return void
     */
    private function getSearchResults($data, $jobs): void
    {
        if (array_key_exists("job_type", $data)) {
            $jobs->where('job_ads.job_ad_type', $data["job_type"]);
        }

        if (auth()->user()->role_id === Roles::CANDIDATE && !array_key_exists("job_position", $data)) {
            $jobs->whereIn('job_ads.title', auth()->user()->candidate->positions->pluck('id')->toArray());
        } elseif (array_key_exists("job_position", $data)){
            $jobs->where('job_ads.title', $data["job_position"]);
        }

        if (array_key_exists("status", $data)) {
            $jobs->where('job_ads.job_ad_status_id', $data["status"]);
        }

        if(array_key_exists("pay_range", $data))
        {
            $pay_range =  explode('-', $data['pay_range']);
            $jobs->whereBetween('job_ads.pay_rate', [$pay_range[0], $pay_range[1]]);
        }
    }
}
