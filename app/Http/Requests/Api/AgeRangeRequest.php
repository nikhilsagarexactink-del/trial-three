<?php

namespace App\Http\Requests\Api;

class AgeRangeRequest extends ApiRequest
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
            'min_age_range' => 'required',
            'max_age_range' => 'required|gt:min_age_range',
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'max_age_range' => 'The maximum range must be greater than minimum age.',
        ];
    }
}
