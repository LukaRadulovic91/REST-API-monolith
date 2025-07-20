<?php

use App\Enums\Roles;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Twilio\TwiML\MessagingResponse;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Auth::routes();

Route::get('/', [App\Http\Controllers\Web\HomeController::class, 'index'])->name('home');
Route::get('/home', [App\Http\Controllers\Web\HomeController::class, 'index']);


/** Client routes */
Route::group(['clients'], static function() {
    Route::prefix('clients')->group(function () {
        Route::get('/', [App\Http\Controllers\Web\ClientsController::class, 'index'])->name('clients.index');
        Route::get('show/{client}', [App\Http\Controllers\Web\ClientsController::class, 'show'])->name('clients.show');
        Route::post('/fetch', [App\Http\Controllers\Web\ClientsController::class, 'fetch'])->name('clients.fetch');
        Route::get('job-ads/{client}', [App\Http\Controllers\Web\ClientsController::class, 'getJobAdsForClient'])->name('clients.get-job-ads');
    });
});
/** Candidate routes */
Route::group(['candidates'], static function() {
    Route::prefix('candidates')->group(function () {
        Route::get('', [App\Http\Controllers\Web\CandidatesController::class, 'index'])->name('candidates.index');
        Route::post('fetch', [App\Http\Controllers\Web\CandidatesController::class, 'fetch'])->name('candidates.fetch');
        Route::get('show/{candidate}', [App\Http\Controllers\Web\CandidatesController::class, 'show'])->name('candidates.show');
        Route::put('recommend-candidate/{candidate}/{jobAd}',
            [\App\Http\Controllers\Web\CandidatesController::class,
                'recommendCandidate'
            ])->name('candidates.recommend-candidate');
    });
});

Route::group(['users'], static function(){
    Route::prefix('users')->group(function (){
        Route::put('approve-user/{user}', [App\Http\Controllers\Web\UsersController::class, 'approveUser'])->name('users.approve-user');
        Route::put('reject-user/{user}', [App\Http\Controllers\Web\UsersController::class, 'rejectUser'])->name('users.reject-user');
        Route::put('read-messages/{user}',  [App\Http\Controllers\Web\UsersController::class, 'readMessages'])->name('users.read-messages');
        Route::get('get-messages', [App\Http\Controllers\Web\InvokeController::class, 'getUnreadMessages'])->name('users.get-messages');
    });
});

Route::prefix('job-ads')->group(function () {
    Route::post('/', [App\Http\Controllers\Web\JobAdsController::class, 'fetch'])->name('job-ads.fetch');
    Route::post('/feedback-fetch', [App\Http\Controllers\Web\JobAdsController::class, 'fetchFeedbacks'])->name('job-ads.feedbacks-fetch');
    Route::get('feedbacks', [App\Http\Controllers\Web\JobAdsController::class, 'showFeedbacks'])->name('job-ads.show-feedbacks');
    Route::get('show-shifts/{jobAd}', [App\Http\Controllers\Web\JobAdsController::class, 'showShifts'])->name('job-ads.show-shifts');
    Route::get('show/{jobAd}', [App\Http\Controllers\Web\JobAdsController::class, 'show'])->name('job-ads.show');
    Route::get('candidates-applied/{jobAd}', [App\Http\Controllers\Web\JobAdsController::class, 'candidatesApplied'])->name('job-ads.candidates-applied');
    Route::put('approve-job-ad/{jobAd}', [App\Http\Controllers\Web\JobAdsController::class, 'approveJobAd'])->name('job-ads.approve-job-ad');
    Route::put('reject-job-ad/{jobAd}', [App\Http\Controllers\Web\JobAdsController::class, 'rejectJobAd'])->name('job-ads.reject-job-ad');
    Route::put('complete-job-ad/{jobAd}', [App\Http\Controllers\Web\JobAdsController::class, 'completeJobAd'])->name('job-ads.complete-job-ad');

    Route::post('payment/{user}/{jobAd}', [App\Http\Controllers\Web\StripeController::class, 'payment'])->name('job-ads.payment');
    Route::get('success/{jobAd}', [App\Http\Controllers\Web\StripeController::class, 'success'])->name('job-ads.success');
    Route::get('cancel/{jobAd}', [App\Http\Controllers\Web\StripeController::class, 'cancel'])->name('job-ads.cancel');
    //Route::get('/', 'App\Http\Controllers\StripeController@checkout')->name('checkout');
});

Route::get('update-card-details/{client}', [App\Http\Controllers\Web\StripeController::class, 'updateCardDetails'])->name('update-card-details');
