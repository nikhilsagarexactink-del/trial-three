<?php

namespace App\Http\Requests\Api;

class UserProductRequest extends ApiRequest
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
            'user_phone' => 'required',
            'user_zip_code' => 'required',
            'product_id' => 'required',
        ];
    }

    /**
     * Validation messages
     */
    public function messages()
    {
        return [
            'user_name.required' => 'User name field require.',
            'user_email.required' => 'User email field require.',
            'user_address.required' => 'User address field require.',
            'user_city.required' => 'User City field require.',
            'user_state.required' => 'User state field require.',
            'user_country.required' => 'User country field require.',
            'user_phone.required' => 'User phone field require.',
            'user_zip_code.required' => 'User zip code field require.',
            'product_id.required' => 'Product id  name field require.',
        ];
    }
}
