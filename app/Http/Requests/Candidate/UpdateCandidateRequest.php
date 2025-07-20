<?php

namespace App\Http\Requests\Candidate;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateCandidateRequest
 *
 * @package App\Http\Requests\Candidate
 */
class UpdateCandidateRequest extends FormRequest
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
        $positions = $this->input('positions');
        $rules = [
            'positions' => 'required|array',
            'transportation' => 'required|integer',
            'desired_positions' => 'required|array',
            'city' =>  'required|string',
            'province' =>  'required|string',
            'postal_code' =>  'required|string',
            'candidateLanguages' => 'required|array|exists:languages,id',
            'softwares' => 'required|array|exists:softwares,id',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email',
            'phone_number' => 'required|string',
            'address' => 'required|string',
            'school' => 'required|string',
        ];
        if (count(array_intersect($positions, [1, 2, 4, 5, 7, 8])) > 0) {
            $rules['year_graduated'] = 'required';
        }
        if (in_array(1, $positions)) {
            $rules['registration'] = 'required';
            $rules['expiry_date'] = 'required';
        }
        return $rules;
    }
}
