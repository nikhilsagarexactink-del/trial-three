<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuoteRequest extends FormRequest
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
            'quote_type' => 'required',
            'author' => 'nullable|max:200',
            'description' => 'required|max:1000',
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
