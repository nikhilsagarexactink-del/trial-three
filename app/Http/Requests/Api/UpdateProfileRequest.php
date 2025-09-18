<?php

namespace App\Http\Requests\Api;

class UpdateProfileRequest extends ApiRequest
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
       $userData = getUser();
       $fields = [
           'first_name' => 'required|max:200',
           'last_name' => 'required|max:200',
           'screen_name' => ! empty($this->input('screen_name')) ? 'check_unique_screen_name' : '',
       ];
       if ($userData->user_type !== 'athlete') {
           $fields['cell_phone_number'] = 'nullable|regex:/^\+\d{1,3}(?:[\s-]?\d){6,15}$/'; // Corrected regex syntax
       }
    //    if ($userData->user_type !== 'athlete') {
    //        $fields['cell_phone_number'] = 'nullable|numeric|regex:/^\d{10,12}$/'; // Corrected regex syntax
    //    }
       if ($userData->user_type == 'athlete') {
           $fields['cell_phone_number'] = 'required|regex:/^\+\d{1,3}(?:[\s-]?\d){6,15}$/'; // Corrected regex syntax
           $fields['age'] = 'required';
           $fields['country'] = 'required';
           $fields['state'] = 'required';
           $fields['city'] = 'required';
           $fields['zip_code'] = 'required|numeric|min:0';
       }

       return $fields;
   }

    /**
     * Custom validation messages
     */
    public function messages()
    {
        return [
            'cell_phone_number.regex' => 'Cell phone number must be a valid number',
            'screen_name.check_unique_screen_name' => 'Screen name Aleredy exist.',
        ];
    }
}
