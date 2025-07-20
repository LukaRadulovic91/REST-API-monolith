<?php

namespace App\Http\Controllers\Web;

use App\Models\User;
use App\Services\User\UserService;
use App\Http\Controllers\Controller;
use App\Notifications\Mails\ApproveUserNotification;
use App\Notifications\Mails\RejectUserNotification;

/**
 * Class UsersController
 *
 * @package App\Http\Controllers\Web
 */
class UsersController extends Controller
{
    /**
     * @UserService
     */
    protected UserService $userService;

    /**
     * Create a new controller instance.
     *
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->middleware('auth');
        $this->userService = $userService;
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function approveUser(User $user): mixed
    {
        $user->notify(new ApproveUserNotification());

        return $this->userService->approveUser($user);
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function rejectUser(User $user): mixed
    {
        $user->notify(new RejectUserNotification());

        return $this->userService->rejectUser($user);
    }

    /**
     * @param User $user
     *
     * @return void
     */
    public function readMessages(User $user): void
    {
        $this->userService->readMessages($user);
    }
}
