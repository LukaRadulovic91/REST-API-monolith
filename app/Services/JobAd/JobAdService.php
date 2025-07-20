<?php

namespace App\Services\JobAd;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Enums\Roles;
use App\Models\User;
use App\NewSampleNotification;
use App\Models\CandidateJobAd;
use App\Models\Shift;
use App\Models\JobAd;
use App\Enums\JobAdStatus;
use App\Enums\JobAdTypes;

/**
 * Class JobAdService
 *
 * @package App\Services\JobAd
 */
class JobAdService
{
    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function storeEntity(Request $request): mixed
    {
        return DB::transaction(function () use ($request) {
            $jobAd = $this->createJobAd($request);
            if ($request->has('shifts')) $this->createShifts($request, $jobAd);
            $this->changeStatus($request, $jobAd);

            return $jobAd;
        });
    }

    /**
     * @param Request $request
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function updateEntity(Request $request, JobAd $jobAd): mixed
    {
        return DB::transaction(function () use ($request, $jobAd) {
            $jobAd = $this->updateJobAd($request, $jobAd);
            if ($request->has('shifts')) $this->updateShifts($request, $jobAd);

            return $jobAd;
        });
    }

    /**
     * @param JobAd $jobAd
     *
     * @return void
     */
    public function deleteJob(JobAd $jobAd)
    {
        $jobAd->delete();
    }

    /**
     * @param JobAd $jobAd
     *
     * @return JobAd
     */
    public function cancelJobAd(JobAd $jobAd): JobAd
    {
        return DB::transaction(function () use ($jobAd) {
            $jobAd->update([
                'job_ad_status_id'  => JobAdStatus::CANCELLED
            ]);
            $this->changeStatusToCanceled($jobAd);

            return $jobAd;
        });
    }

    /**
     * @param JobAd $jobAd
     *
     * @return void
     */
    public function approveJobAd(JobAd $jobAd): void
    {
        $jobAd->update([
            'job_ad_status_id' => JobAdStatus::APPROVED
        ]);
    }

    /**
     * @param JobAd $jobAd
     *
     * @return void
     */
    public function rejectJobAd(JobAd $jobAd): void
    {
        $jobAd->update([
            'job_ad_status_id' => JobAdStatus::REJECTED
        ]);
    }

    /**
     * @param JobAd $jobAd
     * @param array $data
     *
     * @return JobAd
     */
    public function cancelJobAdByCandidate(JobAd $jobAd, array $data): JobAd
    {
        return DB::transaction(function () use ($jobAd, $data) {
            if ($this->checkIfStatusIsApproved($jobAd))
            {
                $jobAd->update([
                    'job_ad_status_id'  => JobAdStatus::APPROVED
                ]);

                // query za usere
                $users = User::rightJoin('expo_tokens as et', 'et.owner_id', '=','users.id')
                    ->join('candidates as c', 'users.id', '=','c.user_id')
                    ->join('candidates_job_ads as cja', 'c.id', '=','cja.candidate_id')
                    ->where(function ($query) use ($jobAd) {
                        $query->where('role_id', '=', Roles::CANDIDATE)
                            ->where('cja.job_ad_id', $jobAd->id);
                    })
                    ->whereNull('users.deleted_at')
                    ->select('users.id')
                    ->get();
                // query za usere

                //slanje notifikacije za usere
                foreach ($users as $user)
                {
                    User::where('id', $user->id)
                        ->first()
                        ->notify(
                            new NewSampleNotification(
                                'Job Ad status has been changed',
                                $jobAd->position->title.' has been approved!'
                            )
                        );
                }
                //slanje notifikacije za usere
            }

            CandidateJobAd::where('job_ad_id', '=', $jobAd->id)
                ->where('candidate_id', '=', auth()->user()->candidate->id)
                ->update([
                    'job_ad_status' => JobAdStatus::CANCELLED,
                    'reason_of_cancellation' => isset($data['cancellation_reason']) ? $data['cancellation_reason'] : ''
                ]);

            return $jobAd;
        });
    }

    /**
     * @return void
     */
    public function dailyChangesOfJobAdStatus(): void
    {
       $completedJobAds =  $this->getCompletedJobAds();
       $this->changeStatusForCompletedJobAds($completedJobAds);
    }

    /**
     * @param JobAd $jobAd
     *
     * @return void
     */
    public function completeJobAd(JobAd $jobAd): void
    {
        $jobAd->update([
            'job_ad_status_id' => JobAdStatus::COMPLETED
        ]);
    }

    /**
     * @return mixed
     */
    private function getCompletedJobAds(): mixed
    {
        return  Shift::whereDate('end_date', '<=', now()->toDateString())
            ->where('end_time', '<=', now()->toTimeString())
            ->groupBy('job_ad_id')
            ->havingRaw('MAX(end_date) <= ?', [now()->toDateString()])
            ->havingRaw('MAX(end_time) <= ?', [now()->toTimeString()])
            ->pluck('job_ad_id');

    }

    /**
     * @param Collection $jobAds
     *
     * @return void
     */
    private function changeStatusForCompletedJobAds(Collection $jobAds): void
    {
        JobAd::whereIn('id', $jobAds)
            ->where([
                ['job_ad_status_id', '!=', JobAdStatus::CANCELLED],
                ['job_ad_status_id', '=', JobAdStatus::BOOKED]
            ])
            ->update(['job_ad_status_id' => JobAdStatus::COMPLETED]);
    }

    /**
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    private function checkIfStatusIsApproved(JobAd $jobAd): mixed
    {
       return CandidateJobAd::where('job_ad_id', '=', $jobAd->id)
            ->where('candidate_id', '=', auth()->user()->candidate->id)
            ->where('job_ad_status', '=', JobAdStatus::BOOKED)
            ->exists();
    }

    /**
     * @param Request $request
     *
     * @return JobAd
     */
    private function createJobAd(Request $request): JobAd
    {
        return JobAd::create(
            array_merge(
                $request->validated(),
                [
                    'client_id' => auth()->user()->client->id,
                    'is_active' => true,
                    'job_ad_status_id' => $this->checkJobType($request)
                ]
            )
        );
    }

    /**
     * @param Request $request
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    private function updateJobAd(Request $request, JobAd $jobAd): mixed
    {

        JobAd::where('id', '=', $jobAd->id)
            ->update(
                array_merge(
                    $request->except(['shifts', 'candidates_feedback', 'user_id']),
                    [
                        'client_id' => auth()->user()->client->id,
                        'is_active' => true,
                        'job_ad_status_id' => $this->checkJobType($request)
                    ]
                ));
        return $jobAd;
    }

    /**
     * @param Request $request
     * @param JobAd $jobAd
     *
     * @return void
     */
    private function createShifts(Request $request, JobAd $jobAd): void
    {
        foreach ($request->shifts as $shift) {
            $start_date = date('Y-m-d', strtotime($shift['start_date']));

            $jobAd->shifts()->create([
                'start_date' => $start_date,
                'end_date' => $shift['end_date'],
                'start_time' => date('H:i:s', strtotime($shift['start_time'])),
                'end_time' => date('H:i:s', strtotime($shift['end_time'])),
            ]);
        }
    }

    /**
     * @param Request $request
     * @param JobAd $jobAd
     *
     * @return void
     */
    private function updateShifts(Request $request, JobAd $jobAd): void
    {
        $shiftIds = collect($request->shifts)->pluck('id')->filter();

        if ($shiftIds->isNotEmpty()) {
            Shift::whereNotIn('id', $shiftIds)->delete();
        }

        foreach ($request->shifts as $shiftData) {
           isset($shiftData['id'] ) ?  $this->updateShift($shiftData, $jobAd) : $this->createShift($shiftData, $jobAd);
        }

    }

    /**
     * @param $shiftData
     * @param $jobAd
     *
     * @return void
     */
    private function updateShift($shiftData, $jobAd): void
    {
       Shift::where('id', '=', $shiftData['id'])->update(
            [
                'start_date' => $shiftData['start_date'],
                'end_date' => $shiftData['end_date'],
                'start_time' => date('H:i:s', strtotime($shiftData['start_time'])),
                'end_time' => date('H:i:s', strtotime($shiftData['end_time'])),
            ]
        );
    }

    /**
     * @param array $shiftData
     * @param JobAd $jobAd
     *
     * @return void
     */
    private function createShift(array $shiftData, JobAd $jobAd): void
    {
        Shift::create([
            'start_date' => $shiftData['start_date'],
            'end_date' => $shiftData['end_date'],
            'start_time' => date('H:i:s', strtotime($shiftData['start_time'])),
            'end_time' => date('H:i:s', strtotime($shiftData['end_time'])),
            'job_ad_id' => $jobAd->id
        ]);
    }

    /**
     * @param Request $request
     * @param JobAd $jobAd
     *
     * @return void
     */
    private function changeStatus(Request $request, JobAd $jobAd): void
    {
        $jobAd->statuses()->create([ 'status' => $this->checkJobType($request) ]);
    }

    /**
     * @param Request $request
     *
     * @return int
     */
    private function checkJobType(Request $request): int
    {
        return $request->job_ad_type === JobAdTypes::TEMPORARY ?
            JobAdStatus::ACTIVE :
            JobAdStatus::PENDING_REVIEW;
    }

    /**
     * @param $jobAd
     *
     * @return void
     */
    private function changeStatusToCanceled(JobAd $jobAd): void
    {
        DB::table('job_ad_statuses')->insert([
            'job_ad_id' => $jobAd->id,
            'status' => JobAdStatus::CANCELLED,
            'created_at' => now(),
            'updated_at' => now(),
            'created_by' => auth()->user()->id,
            'updated_by' => auth()->user()->id
        ]);
    }
}
