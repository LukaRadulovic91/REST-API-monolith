<?php

namespace App\Http\Controllers\API;

use DB;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Services\Clients\ClientService;
use App\Models\Client;
use App\Http\Requests\Client\UpdateClientRequest;
use App\Http\Requests\Client\UpdateOfficeDetailsRequest;
use App\Http\Resources\Mobile\ClientResource;
use App\Http\Controllers\Controller;

/**
 * Class ClientController
 *
 * @package App\Http\Controllers\API
 */
class ClientController extends Controller
{
    /**
     * @var ClientService
     */
    private ClientService $clientService;

    /**
     * Create the controller instance.
     *
     * @param $clientService
     */
    public function __construct(ClientService $clientService)
    {
        $this->clientService = $clientService;
        $this->authorizeResource(Client::class, 'client');
    }

    /**
     * Update resource ability map for current controller
     *
     * @return array
     */
    protected function resourceAbilityMap(): array
    {
        return array_merge(parent::resourceAbilityMap(), [
            'updateClientProfile' => 'updateClientProfile',
            'updateOfficeDetails' => 'updateOfficeDetails',
            'edit' => 'edit'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param Client $client
     *
     * @return JsonResponse
     */
    public function show(Client $client): JsonResponse
    {
        return (new ClientResource($client->with('user', 'user.softwares')->where('clients.id', $client->id)->first()))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Client $client
     * @param ClientService $clientService
     *
     * @return JsonResponse
     */
    public function edit(Client $client, ClientService $clientService): JsonResponse
    {
        return (new JsonResource($clientService->getClient($client)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateClientRequest $request
     * @param Client $client
     *
     * @return JsonResponse
     */
    public function updateClientProfile(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $this->clientService->updateClientProfile($request, $client);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateOfficeDetailsRequest $request
     * @param Client $client
     *
     * @return JsonResponse
     */
    public function updateOfficeDetails(UpdateOfficeDetailsRequest $request, Client $client): JsonResponse
    {
        $this->clientService->updateOfficeDetails($request, $client);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
