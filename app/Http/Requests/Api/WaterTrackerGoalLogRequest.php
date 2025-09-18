<?php

namespace App\Http\Requests\Api;

class WaterTrackerGoalLogRequest extends ApiRequest
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
            'water_value' => 'required|numeric|min:0|max_digits:3',
            'date' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'water_value.min' => 'Please enter the valid water value.',
        ];
    }
}
