<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/email/verify/{id}', [
    \App\Http\Controllers\Auth\VerificationController::class,
    'verify'
])->name('verification.verify');

/** part of API auth system */
Route::middleware(['auth:api', 'user_id', 'verified'])->group(function () {

    /** Client routes */
    Route::group(['clients'], static function() {
        Route::apiResource('clients', '\App\Http\Controllers\API\ClientController');
        Route::get('clients/{client}/edit',
            [\App\Http\Controllers\API\ClientController::class,
                'edit'
            ]);

        Route::post('clients/{client}/change-profile',
            [\App\Http\Controllers\API\ClientController::class,
            'updateClientProfile'
        ]);

        Route::post('clients/{client}/change-office-details',
            [\App\Http\Controllers\API\ClientController::class,
            'updateOfficeDetails'
        ]);
    });

    /** Candidate routes */
    Route::group(['candidates'], static function() {
            Route::post('candidates/{candidate}/update-candidate',
                [\App\Http\Controllers\API\CandidateController::class,
                    'update'
                ]);
        Route::apiResource('candidates', '\App\Http\Controllers\API\CandidateController');
        Route::get('candidates/{candidate}/edit',
            [\App\Http\Controllers\API\CandidateController::class,
                'edit'
            ]);

        Route::put('candidates/{candidate}/{jobAd}/apply-for-job',
            [\App\Http\Controllers\API\CandidateController::class,
                'applyForJob'
            ]);

        Route::get('candidates/{candidate}/{jobAd}/checkIfIsApplied',
            [\App\Http\Controllers\API\CandidateController::class,
                'checkIfIsApplied'
            ]);

        Route::get('candidates/{candidate}/myJobs',
            [\App\Http\Controllers\API\CandidateController::class,
                'myJobs'
            ]);

        Route::get('canidates/{candidate}/{jobAd}/checkIfIsApproved',
            [\App\Http\Controllers\API\CandidateController::class,
                'checkIfIsApproved'
            ]);
    });
    Route::group(['users'], static function() {
        Route::get('user/check-user-status',
            [\App\Http\Controllers\API\UserController::class,
                'checkIsUserApproved'
            ]);

        Route::put('user/toggle-notifications',
            [\App\Http\Controllers\API\UserController::class,
                'toggleNotificationsStatus'
            ]);

        Route::post('user/change-image/{user}',
            [\App\Http\Controllers\API\UserController::class,
                'changeImage'
            ]);

        Route::post('user/{user}/delete',
            [\App\Http\Controllers\API\UserController::class,
                'softDeleteUser'
            ]);
    });
    /** Job Ad routes */
    Route::group(['job-ads'], static function() {
        Route::apiResource('job-ads', '\App\Http\Controllers\API\JobAdController');

        Route::get('job-ads-history',
            [\App\Http\Controllers\API\JobAdController::class,
            'getHistory'
        ]);

        Route::get('job-ads/{jobAd}/candidates-applied',
            [\App\Http\Controllers\API\JobAdController::class,
                'getCandidatesApplied'
            ]);

        Route::put('cancel-job-ad/{jobAd}',
            [\App\Http\Controllers\API\JobAdController::class,
                'cancelJobAdByClient'
            ]);

        Route::put('cancel-job-ad-by-candidate/{jobAd}',
            [\App\Http\Controllers\API\JobAdController::class,
                'cancelJobAdByCandidate'
            ]);

        Route::post('job-ads/{jobAd}/client-feedback',
            [\App\Http\Controllers\API\JobAdController::class,
                'clientFeedback'
            ]);

        Route::post('job-ads/{jobAd}/candidate-feedback',
            [\App\Http\Controllers\API\JobAdController::class,
                'candidateFeedback'
            ]);

        Route::post('job-ads/home',
            [\App\Http\Controllers\API\JobAdController::class,
                'home'
            ]);

        Route::post('job-ads/get-dates',
            [\App\Http\Controllers\API\JobAdController::class,
                'getDates'
            ]);

        Route::put('job-ads/{jobAd}/approve-candidate',
            [\App\Http\Controllers\API\JobAdController::class,
                'approveCandidate'
            ]);


    });
    Route::post('change-password', [\App\Http\Controllers\API\AuthController::class, 'updatePassword']);
    Route::post('logout', [\App\Http\Controllers\API\AuthController::class, 'logout']);

    Route::get('get-invoices/{user}', [App\Http\Controllers\Web\StripeController::class, 'getInvoices'])->name('ap.get-invoices');

});

Route::get('redirect-to-stripe-update-page/{user}', [App\Http\Controllers\Web\StripeController::class, 'redirectToStripeUpdatePage'])->name('api.job-ads.redirect-to-stripe-update-page');

Route::post('login', [\App\Http\Controllers\API\AuthController::class, 'login'])->name('api.login');
Route::post('checkUniqueEmail', [\App\Http\Controllers\API\AuthController::class, 'checkUniqueEmail'], 'api.checkUniqueEmail');
Route::post('register-client', [\App\Http\Controllers\API\AuthController::class, 'registerClient'])->name('api.register-client');
Route::post('register-candidate', [\App\Http\Controllers\API\AuthController::class, 'registerCandidate'])->name('api.register-candidate');
Route::post('create-office-profile/{user}', [\App\Http\Controllers\API\AuthController::class, 'createOfficeProfile'])->name('api.create-office-profile');
Route::post('user/forgot-password', [\App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmailForMobile'])->name('password.email');
Route::post('user/reset-password', [\App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'] )->name('password.update');
/** part of API auth system */

Route::get(
    'twilio/get-messages-by-candidate/{candidate}', [
        \App\Http\Controllers\Twilio\TwilioSmsController::class, 'getLatestMessageByCandidate'
    ]
)->name('twilio.get-message-by-candidate');

Route::any(
    '/twilio/send-message', [
        \App\Http\Controllers\Twilio\TwilioSmsController::class, 'sendMessage'
    ]
)->name('twilio.send-message');


Route::any('/twilio/webhook/message-received', [\App\Http\Controllers\Twilio\TwilioSmsController::class, 'messageReceived'])
    ->name('api.twilio.message-received');

Route::any('/twilio/webhook/status-changed', [\App\Http\Controllers\Twilio\TwilioSmsController::class, 'statusChanged'])
    ->middleware(['is-twilio-request'])
    ->name('api.twilio.status-changed');
