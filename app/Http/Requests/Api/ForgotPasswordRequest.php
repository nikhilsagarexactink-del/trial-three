<?php

namespace App\Http\Requests\Api;

class ForgotPasswordRequest extends ApiRequest
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
            'email' => 'required|regex:'.config('constants.Regex.EMAIL'),
        ];
    }

    public function messages()
    {
        return [
            'email.regex' => 'Please enter a valid email address.',
        ];
    }
}
