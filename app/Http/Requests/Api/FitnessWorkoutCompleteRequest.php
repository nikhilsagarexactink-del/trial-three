<?php

namespace App\Http\Requests\Api;

class FitnessWorkoutCompleteRequest extends ApiRequest
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
            // 'weight' => 'required',
            // 'height' => 'required',
            // 'log_marker' => 'required',
            // 'log_measurement' => 'required',
            // 'log_day' => 'required'
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [];
    }
}
