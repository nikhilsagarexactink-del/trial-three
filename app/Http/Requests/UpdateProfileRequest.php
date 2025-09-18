<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $formData = $this->all();
        $fields = [
            'first_name' => 'required|max:200',
            'last_name' => 'required|max:200',
            'screen_name' => ! empty($this->input('screen_name')) ? 'check_unique_screen_name' : '',
            'cell_phone_number' => 'nullable|regex:/^\+\d{1,3}(?:[\s-]?\d){6,15}$/',
        ];

        if (array_key_exists('favorite_sport', $formData) && count($formData['favorite_sport']) > 0) {
            $fields['favorite_sport_play_years'] = 'numeric|min:0';
        }
        if ($userData->user_type == 'athlete') {
            $fields['age'] = 'required';
            $fields['country'] = 'required|regex:/^[A-Za-z\s]+$/';
            $fields['state'] = 'required|regex:/^[A-Za-z\s]+$/';
            $fields['city'] = 'required|string|regex:/^[A-Za-z\s]+$/';
            $fields['zip_code'] = 'required|digits_between:4,10';
        }

        return $fields;
    }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'cell_phone_number.regex' => 'Cell phone number must be a valid number',
            'screen_name.check_unique_screen_name' => 'Screen name Aleredy exist.',
            'country.regex' => 'Country must only contain letters.',
            'state.regex' => 'State must only contain letters.',
            'city.regex' => 'City must only contain letters.',
            'zip_code.digits_between' => 'Zip code must be numeric and contain between 4 and 10 digits.',
        ];
    }
}
