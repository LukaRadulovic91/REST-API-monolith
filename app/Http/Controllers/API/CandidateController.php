<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use App\Enums\Roles;
use App\Http\Requests\Candidate\UpdateCandidateRequest;
use App\Http\Resources\Mobile\CandidateResource;
use App\Models\JobAd;
use App\Models\User;
use App\NewSampleNotification;
use App\Repositories\API\JobAdRepository;
use App\Services\Candidates\CandidateService;
use App\Models\Candidate;
use App\Http\Controllers\Controller;

/**
 * Class CandidateController
 *
 * @package App\Http\Controllers\API
 */
class CandidateController extends Controller
{
    /**
     * Create the controller instance.
     */
    public function __construct()
    {
        $this->authorizeResource(Candidate::class, 'candidate');
    }

    /**
     * Update resource ability map for current controller
     *
     * @return array
     */
    protected function resourceAbilityMap(): array
    {
        return array_merge(parent::resourceAbilityMap(), [
            'edit' => 'edit',
            'applyForJob' => 'applyForJob',
            'myJobs' => 'myJobs',
            'checkIfIsApproved' => 'checkIfIsApproved'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Candidate $candidate
     * @param CandidateService $candidateService
     *
     * @return JsonResponse
     */
    public function show(Candidate $candidate, CandidateService $candidateService): JsonResponse
    {
        return (new CandidateResource($candidateService->getCandidate($candidate)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param Candidate $candidate
     * @param CandidateService $candidateService
     *
     * @return JsonResponse
     */
    public function edit(Candidate $candidate, CandidateService $candidateService): JsonResponse
    {
        return (new CandidateResource($candidateService->getCandidate($candidate)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param UpdateCandidateRequest $request
     * @param Candidate $candidate
     * @param CandidateService $candidateService
     *
     * @return JsonResponse
     */
    public function update(
        UpdateCandidateRequest $request,
        Candidate $candidate,
        CandidateService $candidateService
    ): JsonResponse
    {
        $candidateService->updateCandidateProfile($request, $candidate);

        return (new CandidateResource($candidateService->getCandidate($candidate)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param Candidate $candidate
     * @param JobAd $jobAd
     * @param CandidateService $candidateService
     *
     * @return JsonResponse
     */
    public function applyForJob(Candidate $candidate, JobAd $jobAd, CandidateService $candidateService): JsonResponse
    {
        if ($candidateService->checkIfCandidateAlreadyApplied($jobAd)) {
            return response()->json(['success' => false, 'message' => 'You already applied for this position.']);
        }
        $candidateService->applyForJob($jobAd);

        $user = User::rightJoin('expo_tokens as et', 'et.owner_id', '=','users.id')
            ->join('clients as c', 'c.user_id', '=', 'users.id')
            ->join('job_ads as ja', 'ja.client_id', '=', 'c.id')
            ->where(function($query) use ($jobAd) {
                $query->where('role_id', '=', Roles::CLIENT)
                      ->where('ja.id', $jobAd->id);
            })
            ->whereNull('users.deleted_at')
            ->select('users.id')
            ->first();

        $user->notify(
            new NewSampleNotification(
                'A candidate has applied for the job you posted.',
                $candidate->user->first_name.' '.
                $candidate->user->last_name .' applied for '.
                $jobAd->position->title . ' position!'
            )
        );

        return response()->json(['success' => true, 'message' => 'Successful job application.']);
    }

    /**
     * @param Candidate $candidate
     * @param JobAdRepository $jobAdRepository
     *
     * @return JsonResponse
     */
    public function myJobs(Candidate $candidate, JobAdRepository $jobAdRepository): JsonResponse
    {
        return (JsonResource::collection($jobAdRepository->getJobAdsForCandidate($candidate)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param Candidate $candidate
     * @param JobAd $jobAd
     * @param CandidateService $candidateService
     *
     * @return void
     */
    public function checkIfIsApproved( Candidate $candidate, JobAd $jobAd, CandidateService $candidateService): void
    {
        if ($candidateService->checkIfCandidateIsApproved($jobAd, $candidate))
        {
            return response()->json(['success' => true, 'message' => 'Candidate is approved.']);
        }

        return response()->json(['success' => false, 'message' => 'Candidate is not approved']);
    }

    /**
     * @param Candidate $candidate
     * @param JobAd $jobAd
     * @param CandidateService $candidateService
     *
     * @return mixed
     */
    public function checkIfIsApplied( Candidate $candidate, JobAd $jobAd, CandidateService $candidateService): mixed
    {
        if ($candidateService->checkIfCandidateIsApplied($jobAd, $candidate))
        {
            return response()->json(['success' => true, 'message' => 'Candidate already applied.']);
        }

        return response()->json(['success' => false, 'message' => 'Candidate did not apply']);
    }
}
