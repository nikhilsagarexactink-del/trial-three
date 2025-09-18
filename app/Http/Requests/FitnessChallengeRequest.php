<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FitnessChallengeRequest extends FormRequest
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
            'user_role_ids' => 'required|array|min:1',
            'days' => 'required|numeric|min:1',
            'name' => 'required',
            'leaderboard' => 'required',
            'live_date' => 'required',
            'type' => 'required',
            'workout_id' => 'required_if:type,Workouts',
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
            'name.required' => 'The title field is required.',
             'user_role_ids.required' => 'The user role field is required.',
             'user_role_ids.min' => 'Please select at least one user role.',
            // 'user_role_ids.min' => '',
        ];
    }

}