<?php

namespace App\Http\Controllers\Web;

use App\Models\Candidate;
use App\Models\JobAd;
use App\Services\Candidates\CandidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Yajra\DataTables\Exceptions\Exception;
use App\Models\Position;
use App\Enums\ProfileStatuses;
use App\Http\Controllers\Controller;
use App\Repositories\CandidateDatatableRepository;

/**
 * Class CandidatesController
 *
 * @package App\Http\Controllers\Web
 */
class CandidatesController extends Controller
{
    /**
     * @var CandidateDatatableRepository
     */
    private CandidateDatatableRepository $candidateDatatableRepository;

    /**
     * Create a new controller instance.
     *
     * @param CandidateDatatableRepository $candidateDatatableRepository
     */
    public function __construct(CandidateDatatableRepository $candidateDatatableRepository)
    {
        $this->middleware('auth');
        $this->candidateDatatableRepository = $candidateDatatableRepository;
    }

    /**
     * @return View
     */
    public function index()
    {
        return view('pages.candidates.index',
            [
                'statuses' => ProfileStatuses::asSelectArray(),
                'positions' => Position::all()->pluck('title', 'id')
            ]);
    }

    /**
     * @param Candidate $candidate
     * @param CandidateService $candidateService
     *
     * @return mixed
     */
    public function show(Candidate $candidate, CandidateService $candidateService): mixed
    {
        return view('pages.candidates.show',
            [
                'candidate' => $candidateService->getCandidate($candidate),
                'messages'  => $candidateService->getMessages($candidate)
            ]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     *
     * @throws Exception
     */
    public function fetch(Request $request)
    {
        $demo = $this->candidateDatatableRepository->getQuery();
        return datatables()->of($demo)
            ->filter(function ($query) use ($request) {
                $this->candidateDatatableRepository->filterByCustomQuery($query, $request->all());
                $this->candidateDatatableRepository->filterByQuery($query, $request->all());
            })
            ->make(true);
    }

    /**
     * @param Request $request
     * @param Candidate $candidate
     * @param JobAd $jobAd
     * @param CandidateService $candidateService
     *
     * @return mixed
     */
    public function recommendCandidate(
        Request $request,
        Candidate $candidate,
        JobAd $jobAd,
        CandidateService $candidateService
    ): mixed
    {
        return $candidateService->recommendCandidate($candidate, $jobAd, $request['recommended']);
    }
}
