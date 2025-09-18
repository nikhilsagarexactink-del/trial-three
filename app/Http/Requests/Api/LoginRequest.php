<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\Requests\Api;

class LoginRequest extends ApiRequest {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        
        return [
            'email' => 'required',
            'password' => 'required',
            'device_id' => 'required',
            'device_type' => 'required',
            'certification_type' => 'required'
        ];
    }
    
    public function messages() {
        return [
            // 'email.regex' => 'Please enter a valid email address.',
        ];
    }

}
