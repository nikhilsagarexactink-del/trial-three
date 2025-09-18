<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'card_number' => 'required',
            'expiry_date' => 'required|max:200',
            'cvc' => 'required|max:3',
            'card_holder_name' => 'required||max:200',
            'country' => 'required',
            'zip_code' => 'required|max:10',
        ];
    }

    /**
     * Validation messages
     */
    public function messages()
    {
        return [];
    }
}
