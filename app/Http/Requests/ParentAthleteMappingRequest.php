<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParentAthleteMappingRequest extends FormRequest
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
            'parent_id' => 'required',
            'athlete_id' => 'required',
        ];
    }

    /**
     * Error messages
     */
    public function messages()
    {
        return [
            'parent_id.required'  => 'Parent field is required',
            'athlete_id.required' => 'Athlete field is required',
        ];
    }
}
