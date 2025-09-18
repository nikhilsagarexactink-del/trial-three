<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StripeCardRequest extends FormRequest
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
            'cardholderName' => 'required|string|max:20',
            // 'payment_method_id' => 'required|string',
        ];
    }
}
