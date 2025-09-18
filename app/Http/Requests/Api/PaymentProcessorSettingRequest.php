<?php

namespace App\Http\Requests\Api;

class PaymentProcessorSettingRequest extends ApiRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'stripe-status' => 'required',
            'stripe-publishable-key' => 'required',
            'stripe-secret-key' => 'required',
            'stripe-webhook-url' => 'nullable|regex:'.config('constants.Regex.URL'),
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'stripe-status.required' => 'The status field is required.',
            'stripe-publishable-key.required' => 'The publishable key field is required.',
            'stripe-secret-key.required' => 'The secret key field is required.',
            'stripe-webhook-url.regex' => 'Please enter the valid url.',
        ];
    }
}
