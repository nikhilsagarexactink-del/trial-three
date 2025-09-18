<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EmailSettingRequest extends FormRequest
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
            // 'driver' => 'required|max:200',
            'mail_host' => 'required|max:200',
            'mail_port' => 'required|max:200',
            'mail_encryption' => 'required|max:200',
            'mail_from_address' => 'required|email|check_email_format|check_email_exists_in_postmark',
            'mail_username' => 'required|max:200',
            'mail_password' => 'required|no_whitespace_allowed|min:8|max:100',
            'mail_from_name' => 'required|max:200',
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
            'mail_from_address.check_email_format' => 'Please enter the valid email',
            'mail_from_address.check_email_exists_in_postmark' => 'Email address not exists in postmark',
        ];
    }
}
