<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GettingStartedRequest extends FormRequest
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
            'title' => 'required|max:200',
            'video_url' => 'nullable|regex:'.config('constants.Regex.URL'),
            'provider_type' => 'nullable',
            'category_id' => 'required',
            'description' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'category_id.required' => 'The category field is required.',
            'description.required' => 'The description field is required.',
        ];
    }
}
