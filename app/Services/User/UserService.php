<?php

namespace App\Services\User;

use DB;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\TwilioSms;
use App\Enums\ProfileStatuses;
use App\Enums\Roles;
use App\Models\ProfileStatus;
use App\Models\User;

/**
 * Class ClientService
 *
 * @package App\Services\Clients
 */
class UserService
{
    /**
     * @param Request $request
     *
     * @return mixed|void
     */
    public function validateDataForUpdatePassword(Request $request)
    {
        return Validator::make($request->all(),
            [
                'current_password' => 'required',
                'new_password' => 'required',
                'new_confirm_password' => 'required|same:new_password'
            ]);
    }

    /**
     * @param string $newPassword
     *
     * @return void
     */
    public function updatePassword(string $newPassword)
    {
        auth()->user()->update([
            'password' => Hash::make($newPassword),
        ]);
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function approveUser(User $user): void
    {
        DB::transaction(static function() use ($user) {
            $user->update([
                'profile_status_id' => ProfileStatuses::APPROVED
            ]);

            ProfileStatus::create([
                'user_id' => $user->id,
                'status' => ProfileStatuses::APPROVED
            ]);
        });
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function rejectUser(User $user): void
    {
        DB::transaction(static function() use ($user) {
            $user->update([
                'profile_status_id' => ProfileStatuses::REJECTED
            ]);

            ProfileStatus::create([
                'user_id' => $user->id,
                'status' => ProfileStatuses::REJECTED
            ]);
        });
    }

    /**
     * @param int $userStatus
     *
     * @return JsonResponse
     */
    public function checkUserStatus(int $userStatus): JsonResponse
    {
        switch ($userStatus) {
            case ProfileStatuses::APPROVED:
                return response()->json(['success' => true, 'message' => 'User approved.']);
            case ProfileStatuses::REJECTED:
                return response()->json(['success' => false, 'message' => 'User is rejected.']);
            default:
                return response()->json(['success' => false, 'message' => 'User is not approved.']);
        }
    }

    /**
     * @param User $user
     * @param $token
     *
     * @return JsonResponse
     */
    public function checkUserLoginStatus(User $user, $token): JsonResponse
    {
        switch ($user->profile_status_id) {
            case ProfileStatuses::REJECTED:
                return response()->json(['success' => false, 'message' => 'User is rejected.'], JsonResponse::HTTP_NOT_ACCEPTABLE);
            case ProfileStatuses::PENDING_REVIEW:
                return response()->json(['success' => false, 'message' => 'User is not approved.'], JsonResponse::HTTP_NOT_ACCEPTABLE);
            default:
                return response()->json([
                    'status' => 'success',
                    'user' => $user,
                    'connected_id' => $user->role_id === Roles::CANDIDATE ? $user->candidate->id : $user->client->id,
                    'authorisation' => [
                        'token' => $token,
                        'type' => 'bearer'
                    ]
                ], JsonResponse::HTTP_OK);
        }
    }

    /**
     * @param User $user
     * @param bool $enableNotifications
     *
     * @return void
     */
    public function toggleNotificationsStatus(User $user, bool $enableNotifications): void
    {
        $user->update([
            'notifications_enabled' => $enableNotifications
        ]);
    }

    /**
     * @param User $user
     * @param Request $request
     *
     * @return void
     *
     * @throws \ReflectionException
     */
    public function changeImage(User $user, Request $request): void
    {
        $user->uploadImage();
        $user->save();
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function readMessages(User $user): void
    {
        TwilioSms::where(function ($query) use ($user){
            $query->where('from', '=', $user->phone_number);
        })
            ->whereNull('read_at')->update([
            'read_at' => Carbon::now()
        ]);
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function getUnreadMessages(User $user): mixed
    {
        return $user->twilioSms;
    }
}
