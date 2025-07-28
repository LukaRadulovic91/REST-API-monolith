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
     * @OA\Get(
     *     path="/api/candidates/{candidate}",
     *     summary="Get a specific candidate",
     *     tags={"Candidates"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="candidate",
     *         in="path",
     *         required=true,
     *         description="Candidate ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Candidate retrieved successfully"),
     *     @OA\Response(response=404, description="Candidate not found")
     * )
     */

    public function show(Candidate $candidate, CandidateService $candidateService): JsonResponse
    {
        return (new CandidateResource($candidateService->getCandidate($candidate)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/candidates/{candidate}/edit",
     *     summary="Fetch candidate data for editing",
     *     tags={"Candidates"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="candidate",
     *         in="path",
     *         required=true,
     *         description="Candidate ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Candidate data for edit retrieved")
     * )
     */

    public function edit(Candidate $candidate, CandidateService $candidateService): JsonResponse
    {
        return (new CandidateResource($candidateService->getCandidate($candidate)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/candidates/{candidate}",
     *     summary="Update candidate profile",
     *     tags={"Candidates"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="candidate",
     *         in="path",
     *         required=true,
     *         description="Candidate ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateCandidateRequest")
     *     ),
     *     @OA\Response(response=200, description="Candidate updated successfully"),
     *     @OA\Response(response=422, description="Validation error")
     * )
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
     * @OA\Post(
     *     path="/api/candidates/{candidate}/jobs/{jobAd}/apply",
     *     summary="Candidate applies for a job",
     *     tags={"Candidates"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="candidate",
     *         in="path",
     *         required=true,
     *         description="Candidate ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="jobAd",
     *         in="path",
     *         required=true,
     *         description="Job Ad ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Applied successfully or already applied"),
     *     @OA\Response(response=500, description="Server error")
     * )
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
     * @OA\Get(
     *     path="/api/candidates/{candidate}/my-jobs",
     *     summary="List of jobs candidate applied to",
     *     tags={"Candidates"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="candidate",
     *         in="path",
     *         required=true,
     *         description="Candidate ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Jobs retrieved successfully")
     * )
     */

    public function myJobs(Candidate $candidate, JobAdRepository $jobAdRepository): JsonResponse
    {
        return (JsonResource::collection($jobAdRepository->getJobAdsForCandidate($candidate)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/candidates/{candidate}/jobs/{jobAd}/is-approved",
     *     summary="Check if candidate is approved for a job",
     *     tags={"Candidates"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="candidate",
     *         in="path",
     *         required=true,
     *         description="Candidate ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="jobAd",
     *         in="path",
     *         required=true,
     *         description="Job Ad ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Approval status returned")
     * )
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
     * @OA\Get(
     *     path="/api/candidates/{candidate}/jobs/{jobAd}/is-applied",
     *     summary="Check if candidate applied for a job",
     *     tags={"Candidates"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="candidate",
     *         in="path",
     *         required=true,
     *         description="Candidate ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="jobAd",
     *         in="path",
     *         required=true,
     *         description="Job Ad ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Application status returned")
     * )
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
