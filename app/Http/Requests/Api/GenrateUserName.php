<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class GenrateUserName extends ApiRequest
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
            'screen_first_name' => 'required|max:100',
            'screen_last_name' => 'required|max:100',
        ];
    }

    public function messages(){
        return [
            'required.screen_first_name' => "First Name field is required.",
            'required.screen_last_name' => "Last Name field is required.",
        ];
    }
}
