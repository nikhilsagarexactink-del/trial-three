<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecipeRequest extends FormRequest
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
            'title' => 'required|max:50',
            'subhead' => 'required|max:50',
            'body' => 'required|max:2000',
            'nutrition_facts' => 'required|max:2000',
            'prep_time' => 'required|numeric|min:0',
            'cook_time' => 'nullable|numeric|min:0',
            'freeze_time' => 'nullable|numeric|min:0',
            //'total_time' => 'required',
            'servings' => 'required|numeric|min:0',
            'fat' => 'required|numeric|min:0',
            'calories' => 'required|numeric|min:0',
            'protein' => 'required|numeric|min:0',
            'carbs' => 'required|numeric|min:0',
            'ingredients' => 'required|max:2000',
            'categories' => 'required|array',
            'directions' => 'required|max:2000',
            'date' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'prep_time.required' => 'The Preparation time field is required.',
            ' prep_time.min' => 'Preparation time must be valid value',
            ' prep_time.numeric' => 'Preparation time must be valid value',
        ];
    }
}
