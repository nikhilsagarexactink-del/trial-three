<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\Requests\Api;

class SignupRequest extends ApiRequest
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
            'name' => 'required|max:200',
            'email' => 'required|check_unique_email|regex:'. config('constants.Regex.EMAIL'),
            'cell_phone' => 'nullable|numeric|digits:10',
            'password' => 'required'
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            // 'cell_phone.required' => 'The phone number field is required.',
            'cell_phone.numeric' => 'The phone number must be a number.',
            'cell_phone.digits' => 'The phone number must be 10 digits.',
            'email.required' => 'Please enter an email address.',
            'email.check_unique_email' => 'Email already exists.',
            'email.regex' => 'Please enter a valid email address.',
            'password.required' => 'Please enter a password.',
        ];
    }
}
