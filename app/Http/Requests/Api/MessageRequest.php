<?php

namespace App\Http\Requests\Api;

class MessageRequest extends ApiRequest
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
            'message' => 'required|max:1000',
            'user_id' => 'required',
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
