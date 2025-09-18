<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AffiliatePayoutSettingRequest extends FormRequest
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
            'payout_method' => 'required|in:billing_credit,paypal,zelle',
            'name' => 'required_if:payout_method,paypal,zelle|nullable|max:200',
            'email' => 'required_if:payout_method,paypal|nullable|email',
            'phone_number' => 'required_if:payout_method,zelle|nullable|numeric|digits:10',
        ];
    }

    public function messages()
    {
        return [
            'name.required_if' => 'The name field is required.',
            'email.required_if' => 'The email field is required.',
            'phone_number.required_if' => 'The phone number field is required.',
        ];
    }
}
