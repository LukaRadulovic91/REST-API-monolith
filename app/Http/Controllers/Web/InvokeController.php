<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\User\UserService;

/**
 * Class InvokeController
 *
 * @package App\Http\Controllers\Web
 */
class InvokeController extends Controller
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
     * @return mixed
     */
    public function getUnreadMessages(): mixed
    {
        return $this->userService->getUnreadMessages(auth()->user());
    }
}
