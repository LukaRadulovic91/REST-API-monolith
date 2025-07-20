<?php

namespace App\Http\Controllers\Web;

use App\Enums\PaymentDays;
use App\Http\Controllers\Controller;
use App\Models\JobAd;
use App\Repositories\API\JobAdRepository;
use App\Repositories\JobAdDatatableRepository;
use App\Repositories\JobAdFeedbackDatatableRepository;
use App\Services\Candidates\CandidateService;
use App\Services\JobAd\JobAdService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Yajra\DataTables\Exceptions\Exception;
use App\Enums\JobAdTypes;
use Illuminate\Support\Arr;

/**
 * Class JobAdsController
 *
 * @package App\Http\Controllers\Web
 */
class JobAdsController extends Controller
{

    /**
     * @var JobAdDatatableRepository
     */
    private JobAdDatatableRepository $jobAdDatatableRepository;

    /**
     * @var JobAdFeedbackDatatableRepository
     */
    private JobAdFeedbackDatatableRepository $jobAdFeedbackDatatableRepository;

    /**
     * Create a new controller instance.
     *
     * @param JobAdDatatableRepository $jobAdDatatableRepository
     * @param JobAdFeedbackDatatableRepository $jobAdFeedbackDatatableRepository
     */
    public function __construct(
        JobAdDatatableRepository $jobAdDatatableRepository,
        JobAdFeedbackDatatableRepository $jobAdFeedbackDatatableRepository
    )
    {
        $this->middleware('auth');
        $this->jobAdDatatableRepository = $jobAdDatatableRepository;
        $this->jobAdFeedbackDatatableRepository = $jobAdFeedbackDatatableRepository;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function fetch(Request $request)
    {
        $demo = $this->jobAdDatatableRepository->getQuery();
        return datatables()->of($demo)
            ->filter(function ($query) use ($request) {
                $this->jobAdDatatableRepository->filterByCustomQuery($query, $request->all());
                $this->jobAdDatatableRepository->filterByQuery($query, $request->all());
            })
            ->make(true);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function fetchFeedbacks(Request $request): JsonResponse
    {
        $demo = $this->jobAdFeedbackDatatableRepository->getQuery();
        return datatables()->of($demo)
            ->filter(function ($query) use ($request) {
                $this->jobAdFeedbackDatatableRepository->filterByCustomQuery($query, $request->all());
                $this->jobAdFeedbackDatatableRepository->filterByQuery($query, $request->all());
            })
            ->make(true);
    }

    /**
     * @return mixed
     */
    public function showFeedbacks(): mixed
    {
        return view('pages.jobAds.show-feedbacks');
    }

    /**
     * @param JobAd $jobAd
     *
     * @return JsonResponse
     */
    public function showShifts(JobAd $jobAd): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $jobAd->shifts,
        ], JsonResponse::HTTP_OK);
    }


    /**
     * @param JobAd $jobAd
     * @param JobAdRepository $jobAdRepository
     *
     * @return mixed
     */
    public function show(JobAd $jobAd, JobAdRepository $jobAdRepository): mixed
    {
        $paymentDays = ($jobAd->job_ad_type === JobAdTypes::PERMANENT_FULL_TIME  || $jobAd->job_ad_type === JobAdTypes::PERMANENT_PART_TIME)
                ? Arr::except(PaymentDays::asSelectArray(), [PaymentDays::TEMP_FIXED_DAYS])
                : Arr::only(PaymentDays::asSelectArray(), [array_key_last(PaymentDays::asSelectArray())]);

        return view('pages.jobAds.show', [
            'jobAd' => $jobAdRepository->getJobAd($jobAd),
            'candidateCancelled' =>  $jobAdRepository->isCancelledByCandidate($jobAd),
            'payment_days' => $paymentDays
        ]);
    }

    /**
     * @param JobAd $jobAd
     * @param CandidateService $candidateService
     *
     * @return mixed
     */
    public function candidatesApplied(JobAd $jobAd, CandidateService $candidateService): mixed
    {
        return response()->json([
            'success' => true,
            'data' => $candidateService->getCandidatesApplied($jobAd)->get(),
        ], JsonResponse::HTTP_OK);
    }


    /**
     * @param JobAd $jobAd
     * @param JobAdService $jobAdService
     *
     * @return mixed
     */
    public function approveJobAd(JobAd $jobAd, JobAdService $jobAdService): mixed
    {
        return $jobAdService->approveJobAd($jobAd);
    }


    /**
     * @param JobAd $jobAd
     * @param JobAdService $jobAdService
     *
     * @return mixed
     */
    public function rejectJobAd(JobAd $jobAd, JobAdService $jobAdService): mixed
    {
        return $jobAdService->rejectJobAd($jobAd);
    }

    /**
     * @param JobAd $jobAd
     * @param JobAdService $jobAdService
     *
     * @return mixed
     */
    public function completeJobAd(JobAd $jobAd, JobAdService $jobAdService): mixed
    {
        return $jobAdService->completeJobAd($jobAd);
    }
}
