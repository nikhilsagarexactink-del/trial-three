<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParentAccountRequest extends FormRequest
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
        return [
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'email' => 'required|email|check_email_format|check_unique_email',
            'password' => 'required|min:8|max:20|no_whitespace_allowed|regex:'.config('constants.Regex.PASSWORD'),
        ];
    }

    public function messages()
    {
        return [
            'email.check_unique_email' => 'The email is already exists.',
            'password.regex' => 'Password must include alphabet,number and special character.',
        ];
    }
}
