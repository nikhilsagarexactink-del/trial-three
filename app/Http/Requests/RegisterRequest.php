<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
        $userType = request()->user_type;
        return [
            'plan_id' => (!empty($userType) && $userType == 'parent') ? '' :'required',
            'subscription_type' => (!empty($userType) && $userType == 'parent') ? '' :'required',
            'first_name' => 'required|max:200',
            'last_name' => 'required|max:200',
            'user_type' => 'required',
            'email' => 'required||check_email_format|check_unique_email',
            'password' => 'required|min:8|max:20|no_whitespace_allowed',
            'password_confirmation' => 'required|same:password',
        ];
    }

    /**
     * Validation messages
     */
    public function messages()
    {
        return [
            'email.check_email_format' => 'Please enter the valid email.',
            'email.check_unique_email' => 'Email already exist.',
            'password.regex' => 'Password must include alphabet,number and special character.',

        ];
    }
}
