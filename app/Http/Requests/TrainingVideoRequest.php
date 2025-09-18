<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TrainingVideoRequest extends FormRequest
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
            'title' => 'required|max:200',
            'video_url' => 'nullable|regex:'.config('constants.Regex.URL'),
            'provider_type' => 'nullable',
            'categories' => 'required|array',
            // 'training_video_category_id' => 'required',
            'skill_levels' => 'required|array',
            'age_ranges' => 'required|array',
            'description' => 'required',
            'date' => 'required',
            'user_types' => 'required',
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'categories.required' => 'Please select the categories.',
            'skill_levels.required' => 'Skill level is required.',
            'age_ranges.required' => 'Age range is required.',
        ];
    }
}
