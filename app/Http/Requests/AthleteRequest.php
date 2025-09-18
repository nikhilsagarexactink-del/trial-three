<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AthleteRequest extends FormRequest
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
        $userData = getUser();
        $id = request()->id;
        $fields = [
            'first_name' => 'required|max:50',
            'last_name' => 'required|max:50',
            'email' => 'required||check_email_format|check_unique_email',
            //'password' => 'required|min:8',
            'age' => 'required',
            'school_name' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'zip_code' => 'required|max:255',
        ];
        if (empty($id)) {
            $fields['password'] = 'required|min:8';
        }

        return $fields;
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'email.check_email_format' => 'Please enter the valid email.',
            'email.check_unique_email' => 'Email already exist.',
        ];
    }
}
