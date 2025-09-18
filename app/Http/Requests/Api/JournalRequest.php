<?php

namespace App\Http\Requests\Api;

// use Illuminate\Foundation\Http\FormRequest;

class JournalRequest extends ApiRequest
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
            'date' => 'required',
            // 'title' => 'required',
            'description' => 'required',
            // 'user_id' => 'required',
        ];
    }

    /**
     * Journal validation messages
     */
    public function messages()
    {
        return [
            'date.required' => 'The date field is required.',
            'title.required' => 'The title field is required.',
            'description.required' => 'The description field is required.',
            'user_id.required' => 'The user id field is required.',
        ];
    }
}
