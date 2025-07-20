<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\VerifiesEmails;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;

/**
 * Class VerificationController
 *
 * @package App\Http\Controllers\Auth
 */
class VerificationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Email Verification Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling email verification for any
    | user that recently registered with the application. Emails may also
    | be re-sent if the user didn't receive the original email message.
    |
    */

    use VerifiesEmails;

    /** @var string  */
    public static $redirectToMobileApp = "https://the-marshall-group-web-app.vercel.app";

    /** @var string  */
    private const CLIENT = "/sign-up/client/congratulations/";

    /** @var string  */
    private const CANDIDATE = "/sign-up/profile-review";

    /**
     * Where to redirect users after verification.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
        $this->middleware('signed')->only('verify');
        $this->middleware('throttle:6,1')->only('verify', 'resend');
    }

    /**
     * Mark the authenticated user's email address as verified.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return RedirectResponse
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function verify(Request $request): RedirectResponse
    {
        $user = User::find($request->route('id'));

        if ($user->hasVerifiedEmail()) {
            return redirect(static::$redirectToMobileApp. '/login/app');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return redirect(
        $user->role_id === Roles::CLIENT ?
            static::$redirectToMobileApp.self::CLIENT.$user->id :
            static::$redirectToMobileApp.self::CANDIDATE
        );
    }
}
