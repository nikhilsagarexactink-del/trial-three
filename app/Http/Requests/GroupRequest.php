<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GroupRequest extends FormRequest
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
            'name' => 'required|check_unique_group_name',
            'athlete_user_ids' => ['required', 'array'],
        ];
    }

    /**
     * Customize validation messages.
     */
    public function messages()
    {
        return [
            'name.required' => 'Group name is required.',
            'name.check_unique_group_name' => 'Group name already exist',
            'athlete_user_ids.required' => 'At least one athlete must be selected.',
        ];
    }
}
