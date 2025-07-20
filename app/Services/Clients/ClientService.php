<?php

namespace App\Services\Clients;

use DB;
use Illuminate\Http\Request;
use App\Enums\JobAdStatus;
use App\Models\Candidate;
use App\Models\CandidateJobAd;
use App\Models\JobAd;
use App\Models\User;
use App\Models\Client;

/**
 * Class ClientService
 *
 * @package App\Services\Clients
 */
class ClientService
{
    /**
     * @param Request $request
     * @param User $user
     *
     * @return Client
     */
    public function createOfficeProfile(Request $request,  User $user): Client
    {
       $self = $this;

       return DB::transaction(static function () use ($user, $request, $self) {
            $user->client()->update($request->except(['city', 'province', 'postal_code', 'softwares', 'user_image_path', 'suite', 'office_number']));
            $self->updateUser($request, $user);
            $user->softwares()->sync($request->softwares);

            return $user->client;
        });
    }

    /**
     * @param Request $request
     * @param Client $client
     *
     * @return bool
     */
    public function updateClientProfile(Request $request, Client $client): bool
    {
       return DB::transaction(static function () use ($request, $client) {
           $client->user()->update([
               'first_name' => $request->first_name,
               'last_name' => $request->last_name,
               'email' => $request->email,
               'phone_number' => $request->phone_number,
               'city' => $request->city,
               'province' => $request->province,
               'postal_code' => $request->postal_code,
               'suite'  => $request->has('suite') ?  $request->suite : null
           ]);

           $client->update([
               'title' => $request->title,
               'dentist_name' => $request->dentist_name,
               'office_address' => $request->office_address,
               'office_number' => $request->office_number,
               'website' => $request->website
           ]);

           return true;
        });
    }

    /**
     * @param Request $request
     * @param Client $client
     *
     * @return bool
     */
    public function updateOfficeDetails(Request $request, Client $client): bool
    {
       return DB::transaction(static function () use ($request, $client) {
           $client->update([
                'type_of_procedure' => $request->type_of_procedure,
                'recall_time' => $request->recall_time,
                'provide_gowns' => $request->provide_gowns,
                'provide_shields' => $request->provide_shields,
                'provide_masks' => $request->provide_masks,
                'digital_x_ray' => $request->digital_x_ray,
                'ultrasonic_cavitron' => $request->ultrasonic_cavitron,
                'free_parking' => $request->free_parking,
                'vaccination_info' => $request->vaccination_info,
                'payment_for_candidates' => $request->payment_for_candidates,
                'tax_deducation' => $request->tax_deducation,
                'sin_info' => $request->sin_info,
//                'piezo' => $request->piezo,
//                'hand_scaling' => $request->hand_scaling,
                'charting' => $request->charting,
           ]);

           $client->user->softwares()->sync($request->softwares);

           return true;
        });
    }

    /**
     * @param array $data
     * @param JobAd $jobAd
     *
     * @return mixed
     */
    public function approveCandidate(array $data, JobAd $jobAd)
    {
        $candidate = Candidate::where('id', $data['candidate_id'])->first()->id;

        return DB::transaction(static function () use ($candidate, $jobAd) {
            $jobAd->update([
               'job_ad_status_id' => JobAdStatus::BOOKED
            ]);

            CandidateJobAd::where('job_ad_id', '=', $jobAd->id)
                ->where('candidate_id', '=', $candidate)
                ->update([
                    'job_ad_status' => JobAdStatus::APPROVED
                ]);

            DB::table('job_ad_statuses')->insert([
                'job_ad_id' => $jobAd->id,
                'status' => JobAdStatus::APPROVED,
                'created_at' => now(),
                'updated_at' => now(),
                'created_by' => auth()->user()->id,
                'updated_by' => auth()->user()->id
            ]);

        });
    }

    /**
     * @param Client $client
     *
     * @return mixed
     */
    public function getClient(Client $client): mixed
    {
        $clientData = $client->with(['user'])->where('id', $client->id)->first();

        $softwareIds = $clientData->user->softwares->map(function ($software) {
            return $software->id;
        })->toArray();

        $clientData->user->unsetRelation('softwares', null);

        $clientData->softwares = $softwareIds;

        return $clientData;
    }

    /**
     * @param JobAd $jobAd
     * @param array $data
     *
     * @return void
     */
    public function clientFeedback(JobAd $jobAd, array $data): void
    {
        $jobAd->update([
            'client_feedback' => $data['feedback'],
            'client_feedback_stars' => $data['stars']
        ]);
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return void
     */
    private function updateUser(Request $request, User $user): void
    {
        $user->update([
            'city' => $request->city,
            'province' => $request->province,
            'postal_code' => $request->postal_code,
        ]);
        if ($request->has('user_image_path')) {
            $user->uploadImage();
            $user->save();
        }
    }
}
