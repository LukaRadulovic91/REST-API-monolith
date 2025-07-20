<?php

namespace App\Http\Resources\Mobile;

use App\Enums\PaymentTime;
use App\Models\JobAdStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Enums\JobAdTypes;
use App\Enums\PaymentForCandidates;

/**
 * Class JobAdResource
 *
 * @package App\Http\Resources\Mobile
 */
class JobAdResource extends JsonResource
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
            'client_id' => $this->client_id,
            'job_ad_type' => JobAdTypes::getDescription($this->job_ad_type),
            'job_ad_status_id' => $this->job_ad_status_id,
            'title' => $this->title ?? $this->position->title,
            'job_description' => $this->job_description,
            'pay_rate' => $this->pay_rate,
            'payment_time' => PaymentTime::getType($this->payment_time),
            'years_experience' => $this->years_experience,
            'permament_start_date' => $this->permament_start_date,
            'candidates_feedback' => $this->candidates_feedback,
            'job_ad_status' => $this->job_ad_status,
            'client_feedback' => $this->client_feedback,
            'is_active' => $this->is_active,
            'lunch_break' => $this->lunch_break,
            'lunch_break_duration' => $this->lunch_break_duration,
            'client' => [
                'company_name' => $this->company_name ?? $this->client->company_name,
                'office_address' => $this->office_address ?? $this->client->office_address
            ],
            'shifts' => isset($this->shifts) ? $this->shifts->map(function ($shift) {
                return [
                    'id' => $shift['id'],
                    'start_date' => $shift['start_date'],
                    'end_date' => $shift['end_date'],
                    'start_time' => date('h:i A', strtotime($shift['start_time'])),
                    'end_time' => date('h:i A', strtotime($shift['end_time'])),
                ];
            }) : [],
             'user' => [
                 'first_name' => $this->first_name ?? $this->client->user->first_name,
                 'last_name' => $this->last_name ?? $this->client->user->last_name,
             ]
        ];
    }
}
