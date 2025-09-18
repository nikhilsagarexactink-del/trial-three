<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ModuleFormRequest extends FormRequest
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
        $isParent = request()->is_parent_menu;
       // echo $isParent;die;
        $fields = [
            'name' => 'required',
            'is_parent_menu' => 'required'
        ];
        if ($isParent == 0) {
            $fields['parent_id'] = 'required';
            $fields['key'] = 'required';
            $fields['url'] = 'required';//'required_if:menu_type,custom-link|regex:'.config('constants.Regex.URL');
        }
        return $fields;
    }

    public function messages()
    {
        return [
            'url.required_if' => 'The url field is required.',
            'parent_id.required' => 'Please select parent menu.',
        ];
    }
}
