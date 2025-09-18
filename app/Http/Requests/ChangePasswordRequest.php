<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePasswordRequest extends FormRequest
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
            'current_password' => 'required|change_password',
            'password' => 'required|confirmed|min:8|max:20|no_whitespace_allowed|regex:'.config('constants.Regex.PASSWORD'),
            'password_confirmation' => 'required|same:password',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            // 'password.required' => 'Password must include alphabet,number and special character.',
            'password.confirmed' => 'Password and Confirm Password should be the same.',
            'password_confirmation.same' => 'The password and confirm password must match.',
            'current_password.change_password' => 'Please enter a valid password.',
            'password.regex' => 'Password must include alphabet,number and special character.',
        ];
    }
}
