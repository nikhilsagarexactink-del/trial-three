<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RewardProductRequest extends FormRequest
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
            'point_cost' => 'required|numeric|min:0',
        ];
    }

    /**
     * Validation messages
     */
    public function messages()
    {
        return [

        ];
    }
}
