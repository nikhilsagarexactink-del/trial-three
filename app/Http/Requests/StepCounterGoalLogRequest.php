<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StepCounterGoalLogRequest extends FormRequest
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
            'step_value' => 'required|numeric|min:0|max_digits:5',
            'date' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'step_value.min' => 'Please enter the valid step value.',
        ];
    }
}
