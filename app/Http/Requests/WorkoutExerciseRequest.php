<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WorkoutExerciseRequest extends FormRequest
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
            'name' => 'required',
            'category_id' => 'required',
            'video_url' => request()->type == 'exercise' ? 'required|regex:'.config('constants.Regex.URL') : '',
            // 'age_range_id' => 'required',
            //'athlete_user_ids' => 'required',
            'duration' => request()->type == 'exercise' ? 'required' : '',
            'difficulty_id' => request()->type == 'workout' ? 'required' : '',
            'total_sets' => request()->type == 'workout' ? 'required|integer|min:1|max:10' : '',
        ];
    }

     public function messages()
     {
         return [
             'category_id.required' => 'The Category field is required.',
             'difficulty_id.required' => 'The Difficulty field is required.',
             'age_range_id.required' => 'The Age Group field is required.',
             'total_sets.required' => 'The set field is required.',
         ];
     }
}
