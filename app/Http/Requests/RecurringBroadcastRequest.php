<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RecurringBroadcastRequest extends FormRequest
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
        // Define the rules based on the trigger_type being 'sign_up'
        $rules = [
            'title' => 'required|max:200',
            'message' => 'required|max:5000',
            'send_type' => 'required',
            'trigger_event' => 'required|string|in:sign_up,day_after_sign_up,last_login,hasnt_logged_in,anniversary',
        ];

         // Conditionally require 'send_time' only if trigger_event is NOT 'sign_up'
            if ($this->input('trigger_event') !== 'sign_up') {
                $rules['send_time'] = 'required';
            }


        // Conditional validation for 'sign_up' trigger type
        if ($this->input('trigger_event') === 'sign_up') {
            $rules['from_day'] = ['required','numeric','lte:to_day','max:0'];
            $rules['to_day'] = ['required','numeric','gte:from_day','max:0'];
        }

        // Additional validation for other fields (e.g. Hasn't Logged In, Anniversary, etc.)
        if ($this->input('trigger_event') === 'has_not_logged_in') {
            $rules['has_not_logged_in_days'] = 'required|integer|in:3,7,10,14,30,60,90,365';
        }

        if ($this->input('trigger_event') === 'anniversary') {
            $rules['anniversary_months'] = 'required|integer|in:1,3,6,12,24,36,48,60,72,84,96,108,120,132,144,156,168,180';
        }

        return $rules;
    }


    /**
     * Validation messages
     */
    public function messages()
    {
        return [
            'from_day.required' => 'The "from day" must be a negative number or zero.',
            'to_day.required' => 'To day must be negative or zero and greater or equal to from day',
        ];
    }
}
