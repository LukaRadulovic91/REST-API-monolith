<?php

namespace App\Services;

use DB;
use Illuminate\Http\Request;
use App\Models\Candidate;
use App\Models\Client;
use App\Models\User;

/**
 * Class CreateProfileService
 *
 * @package App\Services
 */
class CreateProfileService
{
    /**
     * @param User $user
     * @param array $request
     *
     * @return Client
     *
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function storeClient(User $user, array $request)
   {

       $stripe = new \Stripe\StripeClient(config('stripe.sk'));
       $customer = $stripe->customers->create([
           'name' => $user->first_name . ' ' . $user->last_name,
           'email' => $user->email,
           'phone' => $user->phone_number,
           'address' => [
               'line1' => $user->address,
           ],
       ]);

       $client = new Client();
       $client->user_id = $user->id;
       $client->title = $request['title'];
       $client->company_name = $request['company_name'];
       $client->office_number = $request['office_number'];
       $client->stripe_id = $customer->id;
       $client->save();

       return $client;
   }

    /**
     * @param User $user
     * @param Request $request
     *
     * @return void
     */
   public function storeCandidate(User $user, Request $request): void
   {
       $self = $this;
       DB::transaction(function () use ($user, $request, $self) {
           $candidate = new Candidate();
           $candidate->user_id = $user->id;

           // $candidate->positions()->sync($request->positions);

           $candidate->year_graduated = $request->year_graduated;
           $candidate->transportation = $request->transportation;
           $candidate->registration = $request->registration;
           $candidate->expiry_date = $request->expiry_date;
           $candidate->school = $request->school;
           $candidate->save();
           $candidate->uploadCVFile($request->cv_path);
           $candidate->uploadCertificates($request->certificates);
           $candidate->languages()->sync($request->candidateLanguages);
           $user->softwares()->sync($request->softwares);

           $self->createDesiredPositions($request->desired_positions, $candidate);

           $self->createCandidatePositions($request->positions, $candidate);

           $this->updateUserWithCandidate($user, $request);
       });
   }

    /**
     * @param User $user
     * @param Request $request
     *
     * @return void
     */
   private function updateUserWithCandidate(User $user, Request $request): void
   {
       $user->update([
           'city' => $request->city,
           'province' => $request->province,
           'postal_code' => $request->postal_code,
           'image_path' => $request->image_path,
           'address' => $request->address
       ]);
   }

    /**
     * @param array $positions
     * @param Candidate $candidate
     *
     * @return void
     */
   private function createDesiredPositions(array $positions, Candidate $candidate): void
   {
       foreach ($positions as $position)
       {
           DB::table('candidates_desired_position')
               ->insert([
                   'candidate_id' => $candidate->id,
                   'desired_position_id' => $position
               ]);
       }
   }

    /**
     * @param array $positions
     * @param Candidate $candidate
     *
     * @return void
     */
    private function createCandidatePositions(array $positions, Candidate $candidate): void
    {
        foreach ($positions as $position)
        {
            DB::table('candidates_positions')
                ->insert([
                    'candidate_id' => $candidate->id,
                    'position_id' => $position,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        }
    }
}
