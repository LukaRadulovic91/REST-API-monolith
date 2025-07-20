<?php

namespace App\Http\Controllers\Web;

use App\Models\Client;
use App\Repositories\JobAdDatatableRepository;
use Yajra\DataTables\Exceptions\Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use App\Enums\ProfileStatuses;
use App\Http\Controllers\Controller;
use App\Repositories\ClientDatatableRepository;

/**
 * Class ClientsController
 *
 * @package App\Http\Controllers\Web
 */
class ClientsController extends Controller
{

    /**
     * @var ClientDatatableRepository
     */
    private ClientDatatableRepository $clientDatatableRepository;

    /**
     * Create a new controller instance.
     *
     * @param ClientDatatableRepository $clientDatatableRepository
     */
    public function __construct(ClientDatatableRepository $clientDatatableRepository)
    {
        $this->middleware('auth');
        $this->clientDatatableRepository = $clientDatatableRepository;
    }

    /**
     * @return View
     */
    public function index()
    {
        return view('pages.clients.index', ['statuses' => ProfileStatuses::asSelectArray()]);
    }

    /**
     * @param Client $client
     *
     * @return mixed
     */
    public function show(Client $client): mixed
    {
        return view('pages.clients.show', [
            'client' => $client->with('user')->where('id', $client->id)->first()
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
        $demo = $this->clientDatatableRepository->getQuery();
        return datatables()->of($demo)
            ->filter(function ($query) use ($request) {
                $this->clientDatatableRepository->filterByCustomQuery($query, $request->all());
                $this->clientDatatableRepository->filterByQuery($query, $request->all());
            })
            ->make(true);
    }

    /**
     * @param Client $client
     * @param JobAdDatatableRepository $jobAdDatatableRepository
     *
     * @return JsonResponse
     */
    public function getJobAdsForClient(
        Client $client,
        JobAdDatatableRepository $jobAdDatatableRepository
    ): JsonResponse
    {
        $data = $jobAdDatatableRepository->getQuery()->where('client_id', '=', $client->id)->get();

        return response()->json(['data' => $data]);
    }
}
