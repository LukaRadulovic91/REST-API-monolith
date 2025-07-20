<?php

namespace App\Http\Resources\Mobile;

use App\Enums\Charting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\PaymentForCandidates;
use App\Enums\ProvideMasks;
use App\Enums\TypeOfProcedure;
use App\Enums\VaccinationInfo;

/**
 * Class ClientResource
 *
 * @package App\Http\Resources\Mobile
 */
class ClientResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'softwares' => $this->user->softwares->map(function ($software) {
                return [
                    'id' => $software['id'],
                    'title' => $software['title'],
                ];
            }),
            'title' => $this->title,
            'company_name' => $this->company_name,
            'dentist_name' => $this->dentist_name,
            'website' => $this->website,
            'office_address' => $this->office_address,
            'office_number' => $this->office_number,
            'recall_time' => $this->recall_time,
            'type_of_procedure' => TypeOfProcedure::getDescription($this->type_of_procedure),
            'vaccination_info' =>VaccinationInfo::getDescription($this->vaccination_info),
            'provide_masks' => ProvideMasks::getDescription($this->provide_masks),
            'provide_gowns' => $this->provide_gowns,
            'provide_shields' => $this->provide_shields,
            'digital_x_ray' => $this->digital_x_ray,
            'ultrasonic_cavitron' => $this->ultrasonic_cavitron,
            'free_parking' => $this->free_parking,
            'tax_deducation' => $this->tax_deducation,
//            'piezo' => $this->piezo,
//            'hand_scaling' => $this->hand_scaling,
            // 'charting' => Charting::getDescription($this->charting),
            'charting' => $this->charting ? Charting::getDescription($this->charting) : 1,
            'sin_info' => $this->sin_info,
            'user' => [
                'email' => isset($this->user) ? $this->user->email : '' ,
                'first_name' => isset($this->user) ? $this->user->first_name  : '',
                'last_name' => isset($this->user) ? $this->user->last_name : '',
                'phone_number' =>  isset($this->user) ? $this->user->phone_number : '',
                'city' =>  isset($this->user) ? $this->user->city : '',
                'province' =>  isset($this->user) ? $this->user->province : '',
                'postal_code' =>  isset($this->user) ? $this->user->postal_code : '',
                'user_image_path' =>  isset($this->user) ? $this->user->user_image_path : '',
                'suite' =>  isset($this->user) ? $this->user->suite : '',
            ],
            'card_details_setup' => $this->card_details_setup,
            'suite' => $this->suite,
        ];
    }
}
