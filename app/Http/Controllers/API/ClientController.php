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
     * @OA\Get(
     *     path="/api/clients/{client}",
     *     summary="Get a specific client",
     *     tags={"Clients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found"
     *     )
     * )
     */

    public function show(Client $client): JsonResponse
    {
        return (new ClientResource($client->with('user', 'user.softwares')->where('clients.id', $client->id)->first()))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/api/clients/{client}/edit",
     *     summary="Fetch client data for editing",
     *     tags={"Clients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client data retrieved for edit"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */

    public function edit(Client $client, ClientService $clientService): JsonResponse
    {
        return (new JsonResource($clientService->getClient($client)))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/clients/{client}/profile",
     *     summary="Update client's personal profile",
     *     tags={"Clients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateClientRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client profile updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed"
     *     )
     * )
     */
    public function updateClientProfile(UpdateClientRequest $request, Client $client): JsonResponse
    {
        $this->clientService->updateClientProfile($request, $client);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * @OA\Put(
     *     path="/api/clients/{client}/office",
     *     summary="Update client's office details",
     *     tags={"Clients"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="client",
     *         in="path",
     *         required=true,
     *         description="Client ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateOfficeDetailsRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Office details updated successfully"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error"
     *     )
     * )
     */
    public function updateOfficeDetails(UpdateOfficeDetailsRequest $request, Client $client): JsonResponse
    {
        $this->clientService->updateOfficeDetails($request, $client);

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(Response::HTTP_OK);
    }
}
