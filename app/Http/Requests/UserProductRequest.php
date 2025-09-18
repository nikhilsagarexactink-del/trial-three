<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserProductRequest extends FormRequest
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
            'user_name' => 'required',
            'user_email' => 'required',
            'user_address' => 'required',
            'user_city' => 'required',
            'user_state' => 'required',
            'user_country' => 'required',
            'user_phone' => 'required|regex:/^\+\d{1,3}(?:[\s-]?\d){6,15}$/',
            'user_zip_code' => 'required',
            'product_id' => 'required',
        ];
    }
}
