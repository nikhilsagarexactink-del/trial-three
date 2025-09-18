<?php

namespace App\Http\Requests\Api;

class CaptchaSettingRequest extends ApiRequest
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
            'recaptcha-site-key' => 'required',
            'recaptcha-secret-key' => 'required',
            'recaptcha-registration' => 'required',
            'recaptcha-contact-us' => 'required',
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'recaptcha-site-key.required' => 'The site key field is required.',
            'recaptcha-secret-key.required' => 'The secret key field is required.',
            'recaptcha-registration.required' => 'The registration field is required.',
            'recaptcha-contact-us.required' => 'The contact field is required.',
        ];
    }
}
