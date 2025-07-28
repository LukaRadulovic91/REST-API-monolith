<?php

namespace App\Http\Controllers\API;

use Illuminate\{Http\JsonResponse, Http\Resources\Json\JsonResource, Http\Response, Support\Facades\Log};
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Throwable;
use App\NewSampleNotification;
use App\Services\{JobAd\JobAdService, Candidates\CandidateService};
use App\Models\{JobAd, Candidate};
use App\Http\{Requests\Candidate\UpdateCandidateRequest, Resources\Mobile\CandidateResource, Controllers\Controller};

/**
 * @package App\Http\Controllers\API
 * @OA\Tag(name="Candidates", description="Candidate-related endpoints")
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
        try {
            if ($candidateService->checkIfCandidateAlreadyApplied($jobAd)) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already applied for this position.'
                ], Response::HTTP_CONFLICT);
            }

            $candidateService->applyForJob($jobAd);

            $user = $candidateService->getUser($jobAd);

            if ($user) {
                $user->notify(
                    new NewSampleNotification(
                        'A candidate has applied for the job you posted.',
                        sprintf(
                            '%s %s applied for the %s position!',
                            $candidate->user->first_name,
                            $candidate->user->last_name,
                            $jobAd->position->title
                        )
                    )
                );
            }

            return response()->json([
                'success' => true,
                'message' => 'Successful job application.'
            ], Response::HTTP_OK);

        } catch (Throwable $e) {
            Log::error('Job application failed', [
                'candidate_id' => $candidate->id,
                'job_ad_id' => $jobAd->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred during the job application process.'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
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
    public function myJobs(Candidate $candidate, JobAdService $jobAdService): JsonResponse
    {
        return (JsonResource::collection($jobAdService->getAppliedJobs($candidate)))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_OK);
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
    public function checkIfIsApproved( Candidate $candidate, JobAd $jobAd, CandidateService $candidateService): JsonResponse
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
