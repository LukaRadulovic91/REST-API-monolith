<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use App\Http\Requests\Candidate\CancelJobAdRequest;
use App\Http\Requests\DatesRequest;
use App\Http\Requests\Client\ApproveCandidateRequest;
use App\Http\Requests\FeedbackRequest;
use App\Http\Requests\HomeRequest;
use App\Http\Requests\JobAd\UpdateJobAdRequest;
use App\Models\User;
use App\NewSampleNotification;
use App\Services\Candidates\CandidateService;
use App\Services\Clients\ClientService;
use App\Enums\Roles;
use App\Models\JobAd;
use App\Services\JobAd\JobAdService;
use App\Repositories\API\JobAdRepository;
use App\Http\Requests\JobAd\CreateJobAdRequest;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\JobAdResource;

/**
 * Class JobAdController
 *
 * @package App\Http\Controllers\API
 */
class JobAdController extends Controller
{
    /** @var JobAdService */
    private JobAdService $jobAdService;

    /** @var JobAdRepository */
    private JobAdRepository $jobAdRepository;

    /**
     * JobAdController constructor.
     *
     * @param JobAdService $jobAdService
     * @param JobAdRepository $jobAdRepository
     */
    public function __construct(JobAdService $jobAdService, JobAdRepository $jobAdRepository)
    {
        $this->jobAdService = $jobAdService;
        $this->jobAdRepository = $jobAdRepository;

        $this->authorizeResource(JobAd::class);
    }

    /**
     * @OA\Get(
     *     path="/api/job-ads",
     *     summary="Get list of job ads",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/JobAdResource"))
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return (JobAdResource::collection(
            auth()->user()->role_id === Roles::CLIENT ?
                $this->jobAdRepository->getJobAdsForClients(auth()->user()) :
                $this->jobAdRepository->getJobAdsForCandidates()
            ))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/job-ads/home",
     *     summary="Get job ads for home screen with filters",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/HomeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/JobAdResource"))
     *     )
     * )
     */
    public function home(HomeRequest $request)
    {
        return (JobAdResource::collection($this->jobAdRepository->getJobAdsForUsers($request->validated())))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/job-ads",
     *     summary="Create a new job ad",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CreateJobAdRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Job Ad created",
     *         @OA\JsonContent(ref="#/components/schemas/JobAdResource")
     *     )
     * )
     */
    public function store(CreateJobAdRequest $request): JsonResponse
    {
        $jobAd = $this->jobAdService->storeEntity($request);

        // query za usere
        $users = User::rightJoin('expo_tokens as et', 'et.owner_id', '=','users.id')
            ->where('role_id', '=', Roles::CANDIDATE)
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
                        'New Job Ad',
                        'New job ad for '.$jobAd->position->title.' position has been published!'
                    )
                );
        }
        //slanje notifikacije za usere

        return (new JobAdResource($jobAd))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *     path="/api/job-ads/{id}",
     *     summary="Get job ad by ID",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/JobAdResource")
     *     )
     * )
     */
    public function show(JobAd $jobAd, JobAdRepository $jobAdRepository): JsonResponse
    {
        $jobAd = $jobAdRepository->getJobAd($jobAd);

        return (new JobAdResource($jobAd))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/job-ads/{id}",
     *     summary="Update a job ad",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateJobAdRequest")
     *     ),
     *     @OA\Response(
     *         response=202,
     *         description="Job Ad updated",
     *         @OA\JsonContent(ref="#/components/schemas/JobAdResource")
     *     )
     * )
     */
    public function update(UpdateJobAdRequest $request, JobAd $jobAd): JsonResponse
    {
        $jobAd = $this->jobAdService->updateEntity($request, $jobAd);

        return (new JobAdResource($jobAd))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @OA\Delete(
     *     path="/api/job-ads/{id}",
     *     summary="Delete a job ad",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job ad deleted",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function destroy(JobAd $jobAd, JobAdService $jobAdService): JsonResponse
    {
        $jobAdService->deleteJob($jobAd);

        return response()->json(['success' => true, 'message' => 'Job ad successfully deleted']);
    }

    /**
     * @OA\Get(
     *     path="/api/job-ads/{id}/history",
     *     summary="Get job ad history",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(type="array", @OA\Items(ref="#/components/schemas/JobAdResource"))
     *     )
     * )
     */
    public function getHistory(JobAd $jobAd): JsonResponse
    {
        $this->authorize('getHistory', $jobAd);

        return (JobAdResource::collection($this->jobAdRepository->getJobAdsHistory(auth()->user())))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/job-ads/{id}/cancel-by-client",
     *     summary="Cancel job ad by client",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job Ad cancelled",
     *         @OA\JsonContent(ref="#/components/schemas/JobAdResource")
     *     )
     * )
     */
    public function cancelJobAdByClient(
        JobAd $jobAd,
        JobAdService $jobAdService,
        JobAdRepository $jobAdRepository
    ): JsonResponse
    {
        $this->authorize('cancelJobAdByClient', $jobAd);

        $jobAdService->cancelJobAd($jobAd);

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
                        $jobAd->position->title.' has been cancelled!'
                    )
                );
        }
        //slanje notifikacije za usere

        return (new JobAdResource($jobAdRepository->getJobAd($jobAd)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/job-ads/{id}/cancel-by-candidate",
     *     summary="Cancel job ad by candidate",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CancelJobAdRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Job Ad cancelled by candidate",
     *         @OA\JsonContent(ref="#/components/schemas/JobAdResource")
     *     )
     * )
     */
    public function cancelJobAdByCandidate(
        CancelJobAdRequest $cancelJobAdRequest,
        JobAd $jobAd,
        JobAdService $jobAdService,
        JobAdRepository $jobAdRepository
    ): JsonResponse
    {
        $this->authorize('cancelJobAdByCandidate', $jobAd);
        $jobAdService->cancelJobAdByCandidate($jobAd, $cancelJobAdRequest->validated());

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
                'A candidate has cancelled for the job you posted.',
                auth()->user()->first_name.' '.
                auth()->user()->last_name .' has cancelled application '.
                $jobAd->position->title . ' position!'
            )
        );

        return (new JobAdResource($jobAdRepository->getJobAd($jobAd)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/job-ads/{id}/client-feedback",
     *     summary="Submit client feedback for job ad",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FeedbackRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client feedback submitted",
     *         @OA\JsonContent(ref="#/components/schemas/JobAdResource")
     *     )
     * )
     */
    public function clientFeedback(
        FeedbackRequest $request,
        JobAd $jobAd,
        ClientService $clientService
    ): JsonResponse
    {
        $this->authorize('clientFeedback', $jobAd);
        $clientService->clientFeedback($jobAd, $request->validated());

        return (new JobAdResource($this->jobAdRepository->getJobAd($jobAd)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/job-ads/{id}/candidate-feedback",
     *     summary="Submit candidate feedback for job ad",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/FeedbackRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidate feedback submitted",
     *         @OA\JsonContent(ref="#/components/schemas/JobAdResource")
     *     )
     * )
     */
    public function candidateFeedback(
        FeedbackRequest $request,
        JobAd $jobAd,
        CandidateService $candidateService
    ): JsonResponse
    {
        $this->authorize('candidateFeedback', $jobAd);
        $candidateService->candidateFeedback($jobAd, $request->validated());

        return (new JobAdResource($this->jobAdRepository->getJobAd($jobAd)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/api/job-ads/{id}/dates",
     *     summary="Get dates related to a job ad",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/HomeRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dates data",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function getDates(HomeRequest $request, JobAd $jobAd): JsonResponse
    {
        $this->authorize('getDates', $jobAd);

        return response()->json([
            'data' => $this->jobAdRepository->getDates($request->validated()),
        ], Response::HTTP_OK);

    }

    /**
     * @OA\Get(
     *     path="/api/job-ads/{id}/candidates-applied",
     *     summary="Get candidates applied for job ad",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of candidates",
     *         @OA\JsonContent(
     *             @OA\Property(property="other", type="array", @OA\Items()),
     *             @OA\Property(property="recommended", type="array", @OA\Items())
     *         )
     *     )
     * )
     */
    public function getCandidatesApplied(JobAd $jobAd, CandidateService $candidateService): JsonResponse
    {
        $this->authorize('getCandidatesApplied', $jobAd);

        return response()->json([
            'other' => $candidateService->getOtherAppliedCandidates($jobAd),
            'recommended' => $candidateService->getRecommendedCandidates($jobAd)
        ], 200,);
    }


    /**
     * @OA\Post(
     *     path="/api/job-ads/{id}/approve-candidate",
     *     summary="Approve a candidate for a job ad",
     *     tags={"JobAds"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Job Ad ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/ApproveCandidateRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Candidate approved",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function approveCandidate(
        ApproveCandidateRequest $approveCandidateRequest,
        JobAd $jobAd,
        ClientService $clientService
    ): JsonResponse
    {
        $this->authorize('approveCandidate', $jobAd);
        $clientService->approveCandidate($approveCandidateRequest->validated(), $jobAd);

        // query za usere
        $users = User::rightJoin('expo_tokens as et', 'et.owner_id', '=','users.id')
            ->join('candidates as c', 'users.id', '=','c.user_id')
            ->join('candidates_job_ads as cja', 'c.id', '=','cja.candidate_id')
            ->whereNull('users.deleted_at')
            ->where(function ($query) use ($jobAd) {
                $query->where('role_id', '=', Roles::CANDIDATE)
                    ->where('cja.job_ad_id', $jobAd->id);
            })
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
                        $jobAd->position->title.' has been booked!'
                    )
                );
        }
        //slanje notifikacije za usere

        return response()->json(['success' => true, 'message' => 'Candidate approved.']);
    }
}
