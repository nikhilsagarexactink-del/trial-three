<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromoCodeRequest extends FormRequest
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
        $discountType = request()->discount_type;
        $rules = [
            'code' => 'required|check_unique_promo_code',
            'expiration_date' => 'required',
            // [
            //     'required',
            //     'date_format:m-d-Y', // Ensure it's a valid date
            //     'after:today', // Ensure it's after today's date
            // ],
            'no_of_users_allowed' => [
                'required',
                'integer', // Ensure it's an integer
                'min:1',   // Minimum value is 1 (no negative or zero allowed)
            ],
            'plans' => 'required|array|min:1', // Ensure at least one plan is selected
            'discount_amount' => 'required_if:discount_type,amount',
            'discount_percentage' => 'required_if:discount_type,percent',
        ];
        if ($discountType == 'amount') {
            $rules['discount_amount'] = 'required_if:discount_type,amount|numeric';
            $rules['discount_percentage'] = 'required_if:discount_type,percent';
        } else {
            $rules['discount_amount'] = 'required_if:discount_type,amount';
            $rules['discount_percentage'] = 'required_if:discount_type,percent|numeric';
        }

        return $rules;
    }

    /**
     * admin login validation messages
     */
    public function messages()
    {
        return [
            'code.check_unique_promo_code' => 'The promo code must be unique.', // Custom message for unique validation
            'expiration_date.after' => 'The expiration date must be a future date.',
            'plans.required' => 'The plans field is required.', // ✅ Add this line
        ];
    }
}
