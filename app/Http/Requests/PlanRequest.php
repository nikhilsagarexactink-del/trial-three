<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlanRequest extends FormRequest
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
            'name' => 'required|max:200',
            'key' => 'required|check_unique_plan_key',
            'cost_per_month' => 'required|numeric|min:0',
            'cost_per_year' => 'required|numeric|min:0',
            'description' => 'required|max:1000',
            'visibility' => 'required',
            'free_trial_days' => 'required|numeric|min:0',
        ];
    }

    /**
     * Validation messages
     */
    public function messages()
    {
        return [
            'key.check_unique_plan_key' => 'Plan key already exist.',
            'cost_per_month.numeric' => 'Cost per month must be valid.',
            'cost_per_year.numeric' => 'Cost per month must be valid.',
            'cost_per_month.min' => 'Cost per month must be valid.',
            'cost_per_year.min' => 'Cost per month must be valid.',
            'free_trial_days.min' => 'Free Trial days must be valid.',
        ];
    }
}
