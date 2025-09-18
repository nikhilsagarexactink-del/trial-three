<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PermissionToolTipRequest extends FormRequest
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
            'module_id' => 'required',
            'tool_tip_text' => 'required',
        ];
    }

    /**
     * Customize validation messages.
     */
    public function messages()
    {
        return [
            'tool_tip_text.required' => 'Tool tip  text is required.',
            'module_id.required' => 'Module name must be selected.',
        ];
    }
}