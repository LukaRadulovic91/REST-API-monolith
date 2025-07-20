<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateClientRequest
 *
 * @package App\Http\Requests\Client
 */
class CreateClientRequest extends FormRequest
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
            'first_name' => 'string',
            'last_name' => 'string',
            'email' => 'email',
            'title' => 'string',
            'dentist_name' => 'required|string',
            'office_address' => 'required|string',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'website' => 'nullable|string',

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
            'user_image_path' => 'nullable|image|mimes:jpeg,jpg,png,gif',
            'contact_name' => 'required|string',
//            'piezo' => 'required|boolean',
//            'hand_scaling' => 'required|boolean',
            'charting' => 'required|integer'
        ];
    }
}
