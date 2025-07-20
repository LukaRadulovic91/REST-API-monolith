<?php

namespace App\Services\Candidates;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Enums\MediaType;
use App\Models\Candidate;
use App\Models\JobAd;
use App\Models\CandidateJobAd;
use App\Models\JobAdStatus;
use App\Models\Media;
use App\Models\TwilioSms;
use App\Models\User;

/**
 * Class ClientService
 *
 * @package App\Services\Clients
 */
class CandidateService
{

    /**
     * @param Request $request
     * @param Candidate $candidate
     *
     * @return bool
     */
    public function updateCandidateProfile(Request $request, Candidate $candidate): bool
    {
        $self = $this;
       return DB::transaction(static function () use ($request, $candidate, $self) {
              $self->updateUser($request, $candidate->user);

               $candidate->update([
                   'transportation' => $request->transportation,
                   'expiry_date' => $request->has('expiry_date') ?  $request->expiry_date : null,
                   'year_graduated' => $request->has('year_graduated') ?  $request->year_graduated : null,
                   'registration'  => $request->has('registration') ?  $request->registration : null,
                   'school'  => $request->has('school') ?  $request->school : null
               ]);

               if ($request->has('cv_path')) {
                   $candidate->candidateMedia()->wherePivot('type', '=', MediaType::CV)->detach();
                   $candidate->uploadCVFile($request->cv_path);
               }

               if ($request->has('certificates')) {
                   $candidate->candidateMedia()->wherePivot('type', '=', MediaType::CERTIFICATE)->detach();
                   $candidate->uploadCertificates($request->certificates);
               }

               $self->updateDesiredPositions($request->desired_positions, $candidate);

               if ($request->has('positions')) {
                    $self->updatePositions($request->positions, $candidate);
                }

               $candidate->languages()->detach();
               $candidate->languages()->sync($request->candidateLanguages);

               $candidate->user->softwares()->detach();
               $candidate->user->softwares()->sync($request->softwares);

           return true;
        });
    }

    /**
     * Delete media entry from the media table and delete the file from storage.
     *
     * @param Media $media
     */
    private function deleteMediaEntry(Media $media)
    {
        $mediaEntry = Media::find($media['id']);
        if ($mediaEntry) {
            Storage::disk(config('medialibrary.disk_name'))->delete($mediaEntry->file_path);
            $mediaEntry->delete();
        }
    }

    /**
     * @param Candidate $candidate
     *
     * @return mixed
     */
    public function getCandidate(Candidate $candidate): mixed
    {
        $desiredPositions = $candidate
            ->leftJoin('candidates_desired_position as cdp', 'cdp.candidate_id', '=', 'candidates.id')
            ->where('candidates.id', $candidate->id)
            ->pluck('cdp.desired_position_id')
            ->toArray();

        $candidateData = Candidate::with(['user.softwares', 'languages', 'candidateMedia', 'positions'])
            ->where('id', $candidate->id)
            ->first();

        $candidateData->candidate_cv = $candidateData->candidateMedia()->wherePivot('type', MediaType::CV)->get();
        $candidateData->candidate_certificates = $candidateData->candidateMedia()->wherePivot('type', MediaType::CERTIFICATE)->get();
        $candidateData->desired_positions = $desiredPositions;

        // $positions = $candidate->positions->pluck('id')->toArray();
        // $candidateData->positions = $positions;

        return $candidateData;
    }

    /**
     * @param JobAd $jobAd
     * @param array $data
     *
     * @return void
     */
    public function candidateFeedback(JobAd $jobAd, array $data): void
    {
        $jobAd->candidate()->update([
            'candidates_feedback' => $data['feedback'],
            'candidate_feedback_stars' => $data['stars']
        ]);
    }

    /**
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function applyForJob(JobAd $jobAd): mixed
    {
        return DB::transaction(static function () use ($jobAd) {
            CandidateJobAd::create([
                'job_ad_id' => $jobAd->id,
                'candidate_id' => auth()->user()->candidate->id,
                'job_ad_status' => \App\Enums\JobAdStatus::APPLIED,
            ])->save();

            JobAdStatus::create([
                'job_ad_id' => $jobAd->id,
                'status' => \App\Enums\JobAdStatus::APPLIED,
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id
            ])->save();
        });
    }

    /**
     * @param Candidate $candidate
     * @param JobAd $jobAd
     *
     * @return void
     */
    public function recommendCandidate(Candidate $candidate, JobAd $jobAd, string $recommended): void
    {
        CandidateJobAd::where('job_ad_id', '=', $jobAd->id)
            ->where('candidate_id', '=', $candidate->id)
            ->update(['recommended' => $recommended === 'true']);
    }

    /**
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function checkIfCandidateAlreadyApplied(JobAd $jobAd): mixed
    {
        return CandidateJobAd::where('job_ad_id', '=', $jobAd->id)
            ->where('candidate_id', '=', auth()->user()->candidate->id)
            ->whereNot('candidates_job_ads.job_ad_status', '=', \App\Enums\JobAdStatus::CANCELLED)
            ->exists();
    }

    /**
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function getCandidatesApplied(JobAd $jobAd): mixed
    {
        return DB::table('candidates_job_ads as cja')
            ->leftJoin('job_ads as ja', 'cja.job_ad_id', '=', 'ja.id')
            ->leftJoin('candidates as c', 'c.id', '=', 'cja.candidate_id')
            ->leftJoin('users as u', 'u.id', '=', 'c.user_id')
            ->where('ja.id', $jobAd->id)
            ->whereNull('u.deleted_at')
            ->select([
               'u.first_name',
               'u.last_name',
                'u.id',
                'cja.candidate_id',
                'cja.job_ad_status',
                'u.user_image_path',
                'cja.recommended'
            ])
            ->selectRaw('CASE WHEN cja.job_ad_status = 8 THEN true ELSE false END as applied')
            ->selectRaw('CASE WHEN cja.job_ad_status = 2 THEN true ELSE false END as approved');
    }

    /**
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function getRecommendedCandidates(JobAd $jobAd): mixed
    {
       return $this->getCandidatesApplied($jobAd)->where('cja.recommended', true)->get();
    }

    /**
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function getOtherAppliedCandidates(JobAd $jobAd): mixed
    {
        return $this->getCandidatesApplied($jobAd)->where('cja.recommended', false)->get();
    }

    /**
     * @param JobAd $jobAd
     * @param Candidate $candidate
     *
     * @return void
     */
    public function checkIfCandidateIsApproved(JobAd $jobAd, Candidate $candidate)
    {
       return DB::table("candidates_job_ads")
            ->where('job_ad_id', '=', $jobAd->id)
            ->where('candidate_id', '=', $candidate->id)
            ->where('job_ad_status', '=', \App\Enums\JobAdStatus::APPROVED)
            ->exists();
    }

    /**
     * @param JobAd $jobAd
     * @param Candidate $candidate
     *
     * @return mixed
     */
    public function checkIfCandidateIsApplied(JobAd $jobAd, Candidate $candidate): mixed
    {
        return DB::table("candidates_job_ads")
            ->where('job_ad_id', '=', $jobAd->id)
            ->where('candidate_id', '=', $candidate->id)
            ->where('job_ad_status', '=', \App\Enums\JobAdStatus::APPLIED)
            ->exists();
    }

    /**
     * @param Candidate $candidate
     *
     * @return mixed
     */
    public function getMessages(Candidate $candidate): mixed
    {
        return TwilioSms::where('from', 'LIKE', '%' .$candidate->user->phone_number. '%')
            ->orWhere('to', 'LIKE', '%' .$candidate->user->phone_number. '%')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * @param Candidate $candidate
     *
     * @return mixed
     */
    public function getLatestMessageByCandidate(Candidate $candidate): mixed
    {
        return TwilioSms::where('from', 'LIKE', '%' .$candidate->user->phone_number. '%')
            ->orWhere('to', 'LIKE', '%' .$candidate->user->phone_number. '%')
            ->orderBy('created_at', 'desc')
            ->first()
            ->toArray();
    }


    /**
     * @param Request $request
     * @param User $user
     *
     * @return void
     */
    private function updateUser(Request $request, User $user): void
    {
        $user->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
            'address' => $request->address,
            'suite'  => $request->has('suite') ?  $request->suite : null
        ]);
    }

    /**
     * @param array $positions
     * @param Candidate $candidate
     *
     * @return void
     */
    private function updateDesiredPositions(array $positions, Candidate $candidate): void
    {
        DB::table('candidates_desired_position')
            ->where('candidate_id', $candidate->id)
            ->delete();

        foreach ($positions as $position)
        {
            DB::table('candidates_desired_position')
                ->updateOrInsert(
                    [
                        'candidate_id' => $candidate->id,
                        'desired_position_id' => $position,
                        'updated_at' => now()
                    ]
                );
        }
    }

    /**
     * @param array $positions
     * @param Candidate $candidate
     *
     * @return void
     */
    private function updatePositions(array $positions, Candidate $candidate): void
    {
        DB::table('candidates_positions')
            ->where('candidate_id', $candidate->id)
            ->delete();

        foreach ($positions as $position) {
            DB::table('candidates_positions')->insert([
                'candidate_id' => $candidate->id,
                'position_id' => $position,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
