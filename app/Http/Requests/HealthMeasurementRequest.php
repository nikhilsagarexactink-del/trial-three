<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HealthMeasurementRequest extends FormRequest
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
            'height' => 'nullable|numeric',
            'neck' => 'nullable|numeric',
            'shoulder' => 'nullable|numeric', //'regex:/^\d{1,5}$/|min:0',
            'chest' => 'nullable|numeric',
            'waist' => 'nullable|numeric',
            'abdomen' => 'nullable|numeric',
            'hip' => 'nullable|numeric',
            'bicep_left' => 'nullable|numeric',
            'bicep_right' => 'nullable|numeric',
            'thigh_left' => 'nullable|numeric',
            'thigh_right' => 'nullable|numeric',
            'calf_left' => 'nullable|numeric',
            'calf_right' => 'nullable|numeric',
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
