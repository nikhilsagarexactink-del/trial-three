<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoodSettingRequest extends FormRequest
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
            'meals' => 'required|array|min:3',
            'calories_status' => 'nullable',
            'carbohydrates_status' => 'nullable',
            'proteins_status' => 'nullable',
            'status_check' => 'required|in:1', 
        ];
    }

    /**
     * Get the custom validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'meals.min' => 'Please select at least 3 meals a day.',
            'status_check.required' => 'At least one of the status fields must be enabled.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Check if at least one status is enabled
        if ($this->input('calories_status') || 
            $this->input('carbohydrates_status') || 
            $this->input('proteins_status')) {
            $this->merge(['status_check' => 1]);
        } else {
            $this->merge(['status_check' => null]);
        }
    }
}