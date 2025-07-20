<?php

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class CandidateResource
 *
 * @package App\Http\Resources\Mobile
 */
class CandidateResource extends JsonResource
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
            'positions' => $this->positions->map(function ($position) {
                return [
                    'id' => $position->id,
                    'title'=> $position->title,
                ];
            }),
            'year_graduated' => $this->year_graduated,
            'transportation' => $this->transportation,
            'registration'   => $this->registration,
            'expiry_date'    => $this->expiry_date,
            'school'         => $this->school,
            'desired_position' => array_map(function ($position) {
                return [
                    'id' => $position,
                ];
            }, $this->desired_positions),
            'candidate_cv' => $this->candidate_cv,
            'candidate_certificates' =>$this->candidate_certificates->map(function ($certificate) {
                return [
                    'certificate' => $certificate
                ];
            }),
            'user' => [
                'email' => isset($this->user) ? $this->user->email : '' ,
                'first_name' => isset($this->user) ? $this->user->first_name  : '',
                'last_name' => isset($this->user) ? $this->user->last_name : '',
                'phone_number' =>  isset($this->user) ? $this->user->phone_number : '',
                'city' =>  isset($this->user) ? $this->user->city : '',
                'province' =>  isset($this->user) ? $this->user->province : '',
                'postal_code' =>  isset($this->user) ? $this->user->postal_code : '',
                'address' =>  isset($this->user) ? $this->user->address : '',
                'user_image_path'=>  isset($this->user) ? $this->user->user_image_path : '',
                'suite'=>  isset($this->user) ? $this->user->suite : '',
            ],
            'softwares' => $this->user->softwares->map(function ($software) {
                return [
                    'id' => $software['id'],
                ];
            }),
            'languages' => $this->languages->map(function ($language) {
                return [
                    'id' => $language['id'],
                ];
            }),
        ];
    }
}
