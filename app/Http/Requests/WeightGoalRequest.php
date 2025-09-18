<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WeightGoalRequest extends FormRequest
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
     * @return array<-string, mixed>
     */
    public function rules()
    {
        return [
            'weight_goal' => 'required|numeric|min:0|regex:/^\d{1,5}$/',
            'goal_type' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'weight_goal.min' => 'Please enter valid weight',
        ];
    }
}
