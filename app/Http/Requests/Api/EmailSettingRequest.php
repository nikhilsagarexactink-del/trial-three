<?php

namespace App\Http\Requests\Api;

class EmailSettingRequest extends ApiRequest
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
            'driver' => 'required|max:200',
            'host' => 'required|max:200',
            'port' => 'required|max:200',
            'encryption' => 'required|max:200',
            'email' => 'required|email|check_email_format',
            'username' => 'required|max:200',
            'password' => 'required|min:8|max:20|no_whitespace_allowed|regex:'.config('constants.Regex.PASSWORD'),
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            // 'password.required' => 'Password must include alphabet,number and special character.',
            'password.regex' => 'Password must include alphabet,number and special character.',
            'email.check_email_format' => 'Please enter the valid email',
        ];
    }
}
