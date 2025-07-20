<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class PaymentValidationRequest
 *
 * @package App\Http\Requests
 */
class PaymentValidationRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'payment_days' => 'required|numeric',
        ];
    }
}
