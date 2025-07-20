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
     * Display a listing of the resource.
     *
     * @return JsonResponse
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
     * @param HomeRequest $request
     *
     * @return JsonResponse
     */
    public function home(HomeRequest $request)
    {
        return (JobAdResource::collection($this->jobAdRepository->getJobAdsForUsers($request->validated())))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param CreateJobAdRequest $request
     *
     * @return JsonResponse
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
     * @param JobAd $jobAd
     * @param JobAdRepository $jobAdRepository
     *
     * @return JsonResponse
     */
    public function show(JobAd $jobAd, JobAdRepository $jobAdRepository): JsonResponse
    {
        $jobAd = $jobAdRepository->getJobAd($jobAd);

        return (new JobAdResource($jobAd))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param UpdateJobAdRequest $request
     * @param JobAd $jobAd
     *
     * @return JsonResponse
     */
    public function update(UpdateJobAdRequest $request, JobAd $jobAd): JsonResponse
    {
        $jobAd = $this->jobAdService->updateEntity($request, $jobAd);

        return (new JobAdResource($jobAd))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param JobAd $jobAd
     * @param JobAdService $jobAdService
     *
     * @return JsonResponse
     */
    public function destroy(JobAd $jobAd, JobAdService $jobAdService): JsonResponse
    {
        $jobAdService->deleteJob($jobAd);

        return response()->json(['success' => true, 'message' => 'Job ad successfully deleted']);
    }

    /**
     * Display a listing of the resource.
     *
     * @param JobAd $jobAd
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function getHistory(JobAd $jobAd): JsonResponse
    {
        $this->authorize('getHistory', $jobAd);

        return (JobAdResource::collection($this->jobAdRepository->getJobAdsHistory(auth()->user())))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @param JobAd $jobAd
     * @param JobAdService $jobAdService
     * @param JobAdRepository $jobAdRepository
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
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
     * @param CancelJobAdRequest $cancelJobAdRequest
     * @param JobAd $jobAd
     * @param JobAdService $jobAdService
     * @param JobAdRepository $jobAdRepository
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
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
     * @param FeedbackRequest $request
     * @param JobAd $jobAd
     * @param ClientService $clientService
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
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
     * @param FeedbackRequest $request
     * @param JobAd $jobAd
     * @param CandidateService $candidateService
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
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
     * @param HomeRequest $request
     * @param JobAd $jobAd
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function getDates(HomeRequest $request, JobAd $jobAd): JsonResponse
    {
        $this->authorize('getDates', $jobAd);

        return response()->json([
            'data' => $this->jobAdRepository->getDates($request->validated()),
        ], Response::HTTP_OK);

    }

    /**
     * @param JobAd $jobAd
     * @param CandidateService $candidateService
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
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
     * @param ApproveCandidateRequest $approveCandidateRequest
     * @param JobAd $jobAd
     * @param ClientService $clientService
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
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
