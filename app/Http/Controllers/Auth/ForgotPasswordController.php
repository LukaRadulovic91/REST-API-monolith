<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;

/**
 * Class ForgotPasswordController
 *
 * @package App\Http\Controllers\Auth
 */
class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * @param Request $request
     *
     * @return mixed
     */
    public function sendResetLinkEmailForMobile(Request $request)
    {
        $this->validateEmail($request);

        /** @var PasswordBroker $broker */
        $broker = $this->broker();
//        $broker = Password::broker($broker);

        /** @var User $user */
        $user = $broker->getUser($this->credentials($request));

        if ($user === null || ($user !== null && $user->role_id === Roles::ADMIN) )  return response()->json(['success' => false, 'message' => 'Email not found in our system.'], 422);

        $token = $broker->createToken(
            $user
        );

        $expiration = 60;
        $resetLink = config('app.api_base_url') . '/sign-up/forgot-password/create-new-password/app?token=' . $token . '&email=' . $request->get('email'). '&expirationDate=' . urlencode(now()->addMinutes($expiration)->toDateTimeString());
        if ($user->notify(new ResetPasswordNotification($resetLink, 60))) {
            \Log::error('Failed to send welcome email to user.', ['user' => $user]);
            return $this->sendResetLinkFailedResponse($request,Password::INVALID_USER);
        } else {
            return $request->wantsJson()
                ? new JsonResponse(['message' => trans(Password::RESET_LINK_SENT), 'email' => $request->get('email')], 200)
                : back()->with('status', trans(Password::RESET_LINK_SENT));
        }
    }
}
