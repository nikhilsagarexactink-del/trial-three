<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
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
            'password' => 'required|confirmed|regex:'.config('constants.Regex.PASSWORD'),
            'password_confirmation' => 'required',
            'verify_token' => 'required',
        ];
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'password.required' => 'Password must include alphabet,number and special character.',
            'password.confirmed' => 'Password and Confirm Password should be the same.',
            'password_confirmation.required' => 'Please confirm your password.',
            'password.regex' => 'Password must include alphabet,number and special character.',
        ];
    }
}
