<?php

namespace App\Http\Controllers\Web;

use DB;
use App\Enums\Roles;
use App\Models\User;
use App\Models\JobAd;
use App\Models\Client;
use Stripe\StripeClient;
use App\Enums\PaymentDays;
use App\NewSampleNotification;
use App\Models\User as UserModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\PaymentValidationRequest;

/**
 * Class StripeController
 *
 * @package App\Http\Controllers\Web
 */
class StripeController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Foundation\Application
     */
    public function checkout()
    {
        return view('checkout');
    }

    /**
     * @param PaymentValidationRequest $request
     * @param UserModel $user
     * @param JobAd $jobAd
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function payment(PaymentValidationRequest $request, UserModel $user, JobAd $jobAd)
    {
        \Stripe\Stripe::setApiKey(config('stripe.sk'));
        $stripeClient = new \Stripe\StripeClient(config('stripe.sk'));
        $customerId = $user->client->stripe_id;

        $customer = \Stripe\Customer::retrieve($customerId);
        $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;

        $invoice = $stripeClient->invoices->create([
            'customer' => $customerId,
            'description' => 'Invoice for ' . $user->first_name . ' ' . $user->last_name,
        ]);

        $paymentDays = $request->input('payment_days');

        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items'  => [
                [
                    'price_data' => [
                        'currency'     => 'cad',
                        'product_data' => [
                            'name' => 'Job ad title: ' . $jobAd->position->title,
                            'description' => 'Company name: ' .$user->client->company_name
                        ],
                        'unit_amount'  => $paymentDays, // 51
                    ],
                    'quantity'   => 1,
                ],
            ],
            'mode' => 'payment',
            'success_url' => route('job-ads.success', ['jobAd' => $jobAd->id]),
            'cancel_url' => route('job-ads.cancel', ['jobAd' => $jobAd->id]),
            'customer' => $customerId
        ]);

        return redirect()->away($session->url);
    }

    /**
     * @param JobAd $jobAd
     *
     * @return RedirectResponse
     */
    public function success(JobAd $jobAd): RedirectResponse
    {
        $user = User::rightJoin('expo_tokens as et', 'et.owner_id', '=','users.id')
            ->join('clients as c', 'c.user_id', '=', 'users.id')
            ->join('job_ads as ja', 'ja.client_id', '=', 'c.id')
            ->where(function($query) use ($jobAd) {
                $query->where('role_id', '=', Roles::CLIENT)
                    ->where('ja.id', $jobAd->id);
            })
            ->whereNull('users.deleted_at')
            ->select('users.id')
            ->first();

        $user->notify(
            new NewSampleNotification(
                'An invoice for the job you posted has been issued.',
                'An invoice for '.$jobAd->position->title.' has been issued!'
            )
        );

        return redirect()->route('job-ads.show', ['jobAd' => $jobAd->id])
            ->withErrors(__('The payment has been successfully made!'));
    }

    /**
     * @param JobAd $jobAd
     *
     * @return RedirectResponse
     */
    public function cancel(JobAd $jobAd): RedirectResponse
    {
        return redirect()->route('job-ads.show', ['jobAd' => $jobAd->id])
            ->withErrors(__('The payment has been successfully cancelled!'));
    }

    /**
     * @param UserModel $user
     *
     * @return RedirectResponse
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function redirectToStripeUpdatePage(UserModel $user): RedirectResponse
    {
        $stripeCustomerId = $user->client->stripe_id;

        $stripe = new StripeClient(config('stripe.sk'));
        $checkoutSession = $stripe->checkout->sessions->create([
            'customer' => $stripeCustomerId,
            'payment_method_types' => ['card'],
            'mode' => 'setup',
            'success_url' => route('update-card-details', ['client' => $user->client->id]),
            'cancel_url' => 'https://the-marshall-group-web-app.vercel.app/home/update-card?success=false'
        ]);

        return redirect()->to($checkoutSession->url);
    }

    /**
     * @param Client $client
     *
     * @return RedirectResponse
     */
    public function updateCardDetails(Client $client): RedirectResponse
    {
        DB::update('UPDATE clients SET card_details_setup = true WHERE id = '.$client->id);

       return redirect ('https://the-marshall-group-web-app.vercel.app/home/update-card?success=true');
    }

    /**
     * @param UserModel $user
     *
     * @return JsonResponse
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getInvoices(UserModel $user) {
        \Stripe\Stripe::setApiKey(config('stripe.sk'));
        $stripeClient = new \Stripe\StripeClient(config('stripe.sk'));
        $customerId = $user->client->stripe_id;

        $customer = \Stripe\Customer::retrieve($customerId);
        $defaultPaymentMethod = $customer->invoice_settings->default_payment_method;

        $invoices = $stripeClient->invoices->all([
            'customer' => $customerId,
        ]);

        return response()->json($invoices);
    }
}
