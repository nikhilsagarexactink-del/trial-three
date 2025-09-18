<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HealthMarkerRequest extends FormRequest
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
            // 'type' => 'required',
            'weight' => 'numeric|min:0|nullable|regex:/^\d{1,5}$/',
            'body_fat' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            // 'bmi' => 'required',
            'body_water' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'skeletal_muscle' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'weight.min' => 'Please enter valid weight',
            'body_fat.min' => 'Please enter valid Body fat',
            'body_water.min' => 'Please enter valid Body water',
            'skeletal_muscle.min' => 'Please enter valid Skeletal Muscle',
        ];
    }
}
