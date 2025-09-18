<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CalendarEventRequest extends FormRequest
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
        $isRecurring = $this->input('isRecurring');

        $commonRules = [
            'title' => 'required|max:200',
            'start' => 'required|date',
            'end' => [
                'nullable',
                'date',
                'after_or_equal:start', // Replace custom rule if not necessary
            ],
        ];
        if (!empty($isRecurring)) {
            $recurringRules = [
                'isRecurring' => 'required|in:no,daily,weekly,monthly',
                'occurrences' => [
                    'nullable',
                    'numeric',
                    'min:1',
                    'max:10',
                    'required_if:isRecurring,daily',
                    'required_if:isRecurring,weekly',
                    'required_if:isRecurring,monthly',
                ],
            ];

            return array_merge($commonRules, $recurringRules);
        }
        return $commonRules;
    }

    public function messages()
    {
        return [
            'start.required' => 'The date field is required.',
            'end_date_greater_than_start_date' => 'The end date must be greater than start date.',
        ];
    }
}
