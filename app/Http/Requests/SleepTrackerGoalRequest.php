<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SleepTrackerGoalRequest extends FormRequest
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
        // return [
        //     'date' => 'required',
        //     'sleep_duration' => 'required',
        //     'sleep_quality' => 'required',
        // ];
        return [
            'goal' => 'required|numeric|min:0|max:14',
        ];
    }

    public function messages()
    {
        return [];
    }
}
