<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpsellRequest extends FormRequest
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
            'title' => 'required|max:200',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date', // Ensure end_date is not smaller than start_date
            'message' => 'required',
            'frequency' => 'required',
            'location' => 'required',
            'plans' => 'required|array|min:1', // Ensure at least one plan is selected
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, mixed>
     */
    public function messages()
    {
        return [
            'end_date.after_or_equal' => 'The end date must be equal to or later than the start date.',
        ];
    }
}
