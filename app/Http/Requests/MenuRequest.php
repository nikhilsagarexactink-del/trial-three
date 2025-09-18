<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MenuRequest extends FormRequest
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
            'name' => 'required',//check_unique_menu_name
            'url' => 'required',//'required_if:menu_type,custom-link|regex:'.config('constants.Regex.URL'),
        ];
        // if ($isParent == 0) {
        //     $fields['parent_id'] = 'required';
        // }
        return $fields;
    }

    public function messages()
    {
        return [
            'name.check_unique_menu_name' => 'This menu name is already exist.',
            'url.required_if' => 'The url field is required.',
            'parent_id.required' => 'Please select parent menu.',
        ];
    }
}
