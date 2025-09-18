<?php

namespace App\Http\Requests\Api;

class HealthMeasurementRequest extends ApiRequest
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
            'type' => 'required',
            'height' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'neck' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'shoulder' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'chest' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'waist' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'abdomen' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'hip' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'bicep_left' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'bicep_right' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'thigh_left' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'thigh_right' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'calf_left' => 'nullable|numeric|min:0|regex:/^\d{1,5}$/',
            'calf_right' => 'regex:/^\d{1,5}$/|nullable|numeric|min:0',
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'height.min' => 'Please enter valid height',
            'neck.min' => 'Please enter valid neck',
            'shoulder.min' => 'Please enter valid shoulder',
            'chest.min' => 'Please enter valid chest',
            'waist.min' => 'Please enter valid waist',
            'abdomen.min' => 'Please enter valid abdomen',
            'hip.min' => 'Please enter valid hip',
            'bicep_left.min' => 'Please enter valid Bicep left',
            'bicep_right.min' => 'Please enter valid Bicep right',
            'thigh_left.min' => 'Please enter valid Thigh left',
            'thigh_right.min' => 'Please enter valid Thigh right',
            'calf_left.min' => 'Please enter valid calf left',
            'calf_right.min' => 'Please enter valid calf right',
        ];
    }
}
