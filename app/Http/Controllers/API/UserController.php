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
     * @param User $user
     *
     * @param UserService $userService
     * @return JsonResponse
     *
     * @throws AuthorizationException
     */
    public function checkIsUserApproved(User $user, UserService  $userService): JsonResponse
    {
        $this->authorize('checkIsUserApproved', $user);

       return $userService->checkUserStatus(auth()->user()->profile_status_id);
    }

    /**
     * @param NotificationsRequest $request
     * @param User $user
     * @param UserService $userService
     *
     * @return mixed
     *
     * @throws AuthorizationException
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
     * @param ChangeImageRequest $request
     * @param User $user
     * @param UserService $userService
     *
     * @return JsonResponse
     *
     * @throws AuthorizationException
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
