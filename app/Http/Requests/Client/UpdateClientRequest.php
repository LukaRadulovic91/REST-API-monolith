<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateClientRequest
 *
 * @package App\Http\Requests\Client
 */
class UpdateClientRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required',
            'title' => 'required|string',
            'dentist_name' => 'required|string',
            'office_address' => 'required|string',
            'office_number' => 'required',
            'city' => 'required|string',
            'province' => 'required|string',
            'postal_code' => 'required|string',
            'website' => 'nullable|string',
            'contact_name' => 'required|string'
        ];
    }
}
