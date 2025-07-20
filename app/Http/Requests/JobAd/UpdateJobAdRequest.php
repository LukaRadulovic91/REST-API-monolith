<?php

namespace App\Http\Requests\JobAd;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateJobAdRequest
 *
 * @package App\Http\Requests\JobAd
 */
class UpdateJobAdRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|integer',
            'job_description' => 'nullable|string',
            'pay_rate' => 'required|string',
            'payment_time' => 'required|integer',
            'years_experience' => 'nullable|integer',
            'candidates_feedback' => 'string|nullable',
            'permament_start_date' => 'required_if:job_ad_type,1,3|nullable',
            'shifts' => 'required_if:job_ad_type,2|array',
            'shifts.*.start_date' => 'required_if:job_ad_type,2|date_format:Y-m-d|before_or_equal:end_date',
            'shifts.*.end_date' => 'required_if:job_ad_type,2|date_format:Y-m-d|after_or_equal:start_date',
            'shifts.*.start_time' => 'required_if:job_ad_type,2',
            'shifts.*.end_time' => 'required_if:job_ad_type,2',

            'lunch_break' => 'required|boolean',
            'lunch_break_duration' => 'required_if:lunch_break,true',

            'client_feedback' => 'string|nullable',
        ];
    }
}
