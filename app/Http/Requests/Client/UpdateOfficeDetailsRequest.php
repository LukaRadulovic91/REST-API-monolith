<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateOfficeDetailsRequest
 *
 * @package App\Http\Requests\Client
 */
class UpdateOfficeDetailsRequest extends FormRequest
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
            'type_of_procedure' => 'required|integer',
            'recall_time' => 'required|integer',
            'provide_gowns' => 'boolean',
            'provide_shields' => 'boolean',
            'provide_masks' => 'required|integer',
            'digital_x_ray' => 'boolean',
            'ultrasonic_cavitron' => 'boolean',
            'free_parking' => 'boolean',
            'softwares' => 'required|array|exists:softwares,id',
            'vaccination_info' => 'required|integer',
            'payment_for_candidates' => 'required|integer',
            'tax_deducation' => 'boolean',
            'sin_info' => 'boolean',
//            'piezo' => 'required|boolean',
//            'hand_scaling' => 'required|boolean',
            'charting' => 'required|integer'
        ];
    }
}
