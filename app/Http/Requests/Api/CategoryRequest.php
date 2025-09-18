<?php

namespace App\Http\Requests\Api;

class CategoryRequest extends ApiRequest
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
            'name' => 'required|max:200',
        ];
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [];
    }
}
