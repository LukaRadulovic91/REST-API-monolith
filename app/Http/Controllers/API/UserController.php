<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\JsonResponse;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Response;
use App\Models\User;
use App\Http\Requests\NotificationsRequest;
use App\Http\Requests\User\ChangeImageRequest;
use App\Http\Controllers\Controller;
use App\Services\User\UserService;
use Illuminate\Support\Facades\Auth;

/**
 * Class UserController
 *
 * @package App\Http\Controllers\API
 */
class UserController extends Controller
{
    /**
     * Users constructor.
     *
     */
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * @OA\Get(
     *     path="/api/users/{user}/check-approved",
     *     summary="Provera da li je korisnik odobren",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID korisnika",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Status korisnika",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="approved", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=403, description="Nemate dozvolu"),
     *     @OA\Response(response=404, description="Korisnik nije pronađen")
     * )
     */
    public function checkIsUserApproved(User $user, UserService  $userService): JsonResponse
    {
        $this->authorize('checkIsUserApproved', $user);

       return $userService->checkUserStatus(auth()->user()->profile_status_id);
    }

    /**
     * @OA\Post(
     *     path="/api/users/{user}/notifications",
     *     summary="Uključi/isključi notifikacije za korisnika",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID korisnika",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"enable_notifications"},
     *             @OA\Property(property="enable_notifications", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Status notifikacija promenjen"),
     *     @OA\Response(response=403, description="Nemate dozvolu"),
     *     @OA\Response(response=500, description="Greška na serveru")
     * )
     */
    public function toggleNotificationsStatus(
        NotificationsRequest $request,
        User $user,
        UserService $userService
    ): mixed
    {
        $this->authorize('toggleNotificationsStatus', $user);

        try {
            $userService->toggleNotificationsStatus(auth()->user(), $request->enable_notifications);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => $request->enable_notifications ? 'Notifications successfully enabled' : 'Notifications successfully disabled',
        ], JsonResponse::HTTP_OK);

    }

    /**
     * @OA\Post(
     *     path="/api/users/{user}/change-image",
     *     summary="Promeni sliku korisnika",
     *     tags={"Users"},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="ID korisnika",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"image"},
     *                 @OA\Property(
     *                     description="Nova slika korisnika",
     *                     property="image",
     *                     type="string",
     *                     format="binary"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Slika uspešno promenjena"),
     *     @OA\Response(response=403, description="Nemate dozvolu"),
     *     @OA\Response(response=500, description="Greška na serveru")
     * )
     */
    public function changeImage(
        ChangeImageRequest $request,
        User $user,
        UserService $userService
    ): JsonResponse
    {
        $this->authorize('changeImage', $user);

        try {
            $userService->changeImage($user, $request);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'Picture successfully changed',
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @param User $user
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function softDeleteUser(User $user): JsonResponse
    {
        $this->authorize('softDeleteUser', $user);

        try {
            Auth::logout();
            $user->delete();
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'message' => 'The user has been successfully deleted!',
        ], JsonResponse::HTTP_OK);
    }
}
