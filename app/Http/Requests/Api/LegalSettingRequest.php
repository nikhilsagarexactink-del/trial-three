<?php

namespace App\Http\Requests\Api;

class LegalSettingRequest extends ApiRequest
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
            'terms-of-service-url' => 'required',
            'privacy-policy-url' => 'required',
            'cookie-policy-url' => 'required',
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'terms-of-service-url.required' => 'The terms of service field is required.',
            'privacy-policy-url.required' => 'The privacy policy field is required.',
            'cookie-policy-url.required' => 'The cookie policy field is required.',
        ];
    }
}
